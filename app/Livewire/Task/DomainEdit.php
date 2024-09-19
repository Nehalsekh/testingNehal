<?php

namespace App\Livewire\Domain;

use App\Models\BusinessUnit;
use App\Models\EmailDomain;
use Livewire\Component;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;

use Livewire\WithFileUploads;
use Illuminate\Support\Facades\File;

class DomainEdit extends Component
{
    use LivewireAlert;
    use WithFileUploads;
    public $domainId;
    public $domainInfo = [];
    public $businessUnit;
    public $provider;
    public $projectId;
    public $clientId;
    public $clientSecret;
    public $tenantId;
    public $domainName;
    public $authFile;
    public $showExtraDiv = false;
    public $showExtraGmail = true;
    protected $rules = [
        'authFile' => 'required|file|mimes:json',
    ];
    protected $listeners = [
        'domainEdit',
        'domainDeactive',
        'confirmDomainDeactive',
        'domainActive',
        'confirmDomainActive',
        'delete',
        'confirmDelete'
    ];

    public function mount()
    {
        $this->domainInfo['domainName']     = '';
        $this->domainInfo['businessUnit']   = '';
        $this->domainInfo['provider']       = '';
        $this->domainInfo['projectId']      = '';
        $this->domainInfo['clientId']       = '';
        $this->domainInfo['clientSecret']   = '';
        $this->domainInfo['tenantId']       = '';
        $this->domainInfo['authFile']       = '';
    }

    public function domainEdit($domainId)
    {
        $this->providerStore                = EmailDomain::all();
        $this->domainId                     = $domainId;
        $emailDomains                       = EmailDomain::find($this->domainId);
        $this->domainInfo['domainName']     = $emailDomains->domain_name;
        $this->domainInfo['businessUnit']   = $emailDomains->business_unit;
        $this->domainInfo['provider']       = $emailDomains->provider;
        if ($this->domainInfo['provider'] === 'Outlook') {
            $this->showExtraDiv             = true;
        }
        $this->domainInfo['projectId']      = $emailDomains->project_id;
        $this->domainInfo['clientId']       = $emailDomains->client_id;
        $this->domainInfo['clientSecret']   = $emailDomains->client_secret;
        $this->domainInfo['tenantId']       = $emailDomains->tenant_id;
        $this->domainInfo['authFile']       = $emailDomains->auth_file;

        $this->dispatch('edit-domain-modal');
    }
    public function domainDeactive($domainId): void
    {

        $this->domainId = $domainId;
        $this->alert('warning', 'Are you sure you want to deactivate this domain?', [
            'icon'              => 'warning',
            'showConfirmButton' => true,
            'showCancelButton'  => true,
            'confirmButtonText' => 'Deactivate',
            'cancelButtonText'  => 'Cancel',
            'allowOutsideClick' => false,
            'timer'             => null,
            'position'          => 'center',
            'onConfirmed'       => 'confirmDomainDeactive', // Pass domainId to the callback
        ]);
    }

    public function domainActive($domainId): void
    {
        $this->domainId = $domainId;
        $this->alert('warning', 'Are you sure you want to Active this domain?', [
            'icon' => 'success',
            'showConfirmButton' => true,
            'showCancelButton' => true,
            'confirmButtonText' => 'Active',
            'cancelButtonText' => 'Cancel',
            'allowOutsideClick' => false,
            'timer' => null,
            'position' => 'center',
            'onConfirmed' => 'confirmDomainActive', // Pass domainId to the callback
        ]);
    }

    protected function messages()
    {
        return [
            'domainInfo.provider.required' => 'The provider field is required.',
            'domainInfo.authFile.required' => 'The json file is required.',
            'domainInfo.authFile.file' => 'The json file must be a file.',
            'domainInfo.authFile.mimes' => 'The json file must be a JSON file.',
            'domainInfo.domainName.required' => 'The domain name field is required.',
            'domainInfo.domainName.regex' => 'The domain name format is invalid.',
            'domainInfo.domainName.unique' => 'The domain name has already been taken.',
        ];
    }

    public function updateDomainInfo()
    {
         // dd($this->domainInfo);
         // dd($this->domainInfo['authFile']->getClientOriginalName());
        if ($this->domainInfo['provider'] == "Gmail") {

            $authFile = EmailDomain::find($this->domainId);
            // && $this->domainInfo['authFile']
            // dd($authFile);
            $this->validate([
                'domainInfo.provider'   => 'required',
                'domainInfo.authFile'   => 'required|file|mimes:json', // Corrected validation
                'domainInfo.domainName' => ['required', 'regex:/^[a-zA-Z0-9.-]+\.[a-zA-Z]{2,3}$/'],
            ], $this->messages());


            $authFileName = $this->domainInfo['authFile']->getClientOriginalName();
            $absolutePath = public_path() . "/" . "email_auth_file";
            File::isDirectory($absolutePath) or File::makeDirectory($absolutePath, 0777, true, true);
            $attachmentContent = $this->domainInfo['authFile']->get();
            $filePath = $absolutePath . "/" . $authFileName;
            file_put_contents($filePath, $attachmentContent);
            chmod($absolutePath . '/' . $authFileName, 0777);

            $domainInfo = [
                'domain_name'   => $this->domainInfo['domainName'],
                'business_unit' => $this->domainInfo['businessUnit'],
                'provider'      => $this->domainInfo['provider'],
                'auth_file'     => $authFileName,
                "project_id"    => null,
                "tenant_id"     => null,
                "client_id"     => null,
                "client_secret" => null
            ];

            // dd($domainInfo);
            EmailDomain::where('id', $this->domainId)->update($domainInfo);

            /*if (!$authFile->auth_file) {
            } else {
            }*/

            $this->flash('success', 'Domain Update Successfully', [
                'position'  => 'center',
                'timer'     => 3000,
                'toast'     => false,
                'icon'      => 'success',
            ], '/domain-mailbox');

        } else {

            if ($this->domainId) {
                $this->validate([
                    'domainInfo.provider'   => 'required',
                    'domainInfo.domainName' => ['required', 'regex:/^[a-zA-Z0-9.-]+\.[a-zA-Z]{2,3}$/'],

                ]);

                if ($this->domainInfo['provider'] == "Outlook") {
                    $this->validate([
                        'domainInfo.projectId' => 'required',
                    ]);
                }

                $domainInfo = [
                    'domain_name'   => $this->domainInfo['domainName'],
                    'business_unit' => $this->domainInfo['businessUnit'],
                    'provider'      => $this->domainInfo['provider'],
                    'auth_file'     => null,
                    'project_id'    => $this->domainInfo['projectId'],
                    'client_id'     => $this->domainInfo['clientId'],
                    'client_secret' => $this->domainInfo['clientSecret'],
                    'tenant_id'     => $this->domainInfo['tenantId'],
                ];

                EmailDomain::where('id', $this->domainId)->update($domainInfo);

            }

            /*else {
                $authFile = EmailDomain::find($this->domainId);
                $domainInfo = [
                    'domain_name'   => $this->domainInfo['domainName'],
                    'business_unit' => $this->domainInfo['businessUnit'],
                    'provider'      => $this->domainInfo['provider'],
                    'authFile'      => $authFile->auth_file,
                    "project_id"    => null,
                    "tenant_id"     => null,
                    "client_id"     => null,
                    "client_secret" => null
                ];
                EmailDomain::where('id', $this->domainId)->update($domainInfo);
            }*/

            $this->flash('success', 'Domain Update Successfully', [
                'position'  => 'center',
                'timer'     => 3000,
                'toast'     => false,
                'icon'      => 'success',
            ], '/domain-mailbox');
        }

    }
    public function confirmDomainDeactive(): void
    {
        EmailDomain::where('id', $this->domainId)->update(['status' => 'Inactive']);
        $this->flash('success', 'Domain Deactivete Successfully', [
            'position'  => 'center',
            'timer'     => 3000,
            'toast'     => false,
            'icon'      => 'success',
        ], '/domain-mailbox');
    }
    public function confirmDomainActive(): void
    {
        EmailDomain::where('id', $this->domainId)->update(['status' => 'Active']);
        $this->flash('success', 'Domain Active Successfully', [
            'position'  => 'center',
            'timer'     => 3000,
            'toast'     => false,
            'icon'      => 'success',
        ], '/domain-mailbox');
    }
    public function delete($domainId)
    {
        $this->domainId = $domainId;
        $this->alert('warning', 'Are you sure you want to Delete this domain?', [
            'icon'              => 'warning',
            'showConfirmButton' => true,
            'showCancelButton'  => true,
            'confirmButtonText' => 'Delete',
            'cancelButtonText'  => 'Cancel',
            'allowOutsideClick' => false,
            'timer'             => null,
            'position'          => 'center',
            'onConfirmed'       => 'confirmDelete',
        ]);
    }

    public function removeAuthFile()
    {
        $this->domainInfo['authFile'] = null;
        // dd($this->domainInfo);
    }

    public function confirmDelete(): void
    {
        EmailDomain::where('id', $this->domainId)->delete();
        $this->flash('success', 'Domain Delete  Successfully', [
            'position'  => 'center',
            'timer'     => 3000,
            'toast'     => false,
            'icon'      => 'success',
        ], '/domain-mailbox');
    }

    public function toggleExtraDiv()
    {
        $providerValueStore = $this->domainInfo['provider'];
        if ($providerValueStore == "Outlook") {
            $this->showExtraDiv = true;
        }
    }

    public function render()
    {
        $providerStore = ["Gmail", "Outlook"];
        $businessUnits = BusinessUnit::pluck('name');
        return view('livewire.pages.domain.domain-edit', compact('businessUnits', 'providerStore'));
    }
}
