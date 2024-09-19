<?php

namespace App\Livewire\Domain;
use App\Livewire\CustomComponent;

use App\Models\EmailDomain;

class DomainView extends CustomComponent
{
    public $domainId;
    public $domainInfo=[];
    public $businessUnit;
    public $provider;
    public $projectId;
    public $clientId;
    public $clientSecret;
    public $tenantId;

    protected $listeners = ['viewDomain'];
    public function viewDomain($domainId)
    {
        $this->domainId                     = $domainId;
        $emailDomains                       = EmailDomain::find($this->domainId);
        $this->domainInfo['domainName']     = $emailDomains->domain_name;
        $this->domainInfo['businessUnit']   = $emailDomains->business_unit;
        $this->domainInfo['provider']       = $emailDomains->provider;

        $this->domainInfo['projectId']      = $emailDomains->project_id;

        $this->domainInfo['clientId']       = $emailDomains->client_id;
        $this->domainInfo['clientSecret']   = $emailDomains->client_secret;
        $this->domainInfo['tenantId']       = $emailDomains->tenant_id;
        $this->domainInfo['authFile']       = $emailDomains->auth_file;

        $this->dispatch('view-domain-modal');
    }
    public function render()
    {
        return view('livewire.pages.domain.domain-view');
    }
}
