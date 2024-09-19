<?php

namespace App\Livewire\Domain;

use App\Models\EmailDomain;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Blade;
use PowerComponents\LivewirePowerGrid\Button;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Exportable;
use PowerComponents\LivewirePowerGrid\Facades\Filter;
use PowerComponents\LivewirePowerGrid\Facades\Rule;
use PowerComponents\LivewirePowerGrid\Footer;
use PowerComponents\LivewirePowerGrid\Header;
use PowerComponents\LivewirePowerGrid\PowerGrid;
use PowerComponents\LivewirePowerGrid\PowerGridColumns;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;
use PowerComponents\LivewirePowerGrid\Traits\WithExport;

final class DomainListTable extends PowerGridComponent
{
   // use WithExport;

    protected $slNo = 0;
    public function datasource(): Builder
    {
        return EmailDomain::query()->orderBy('created_at', 'desc');
    }
    public function addColumns(): PowerGridColumns
    {
        return PowerGrid::columns()
            ->addColumn('slNo',function (EmailDomain $model) {
                return ++$this->slNo;
            })
            ->addColumn('domain_name')
            ->addColumn('domain_name_lower', fn(EmailDomain $model) => strtolower(e($model->domain_name)))
            ->addColumn('provider')
            ->addColumn('status')
            ->addColumn('created_at_formatted', fn(EmailDomain $model) => Carbon::parse($model->created_at)->format('d-m-y'));
    }

    public function columns(): array
    {
        return [
            Column::make('SL NO', 'slNo'),
            Column::make('Domain name', 'domain_name')->searchable(),
            Column::make('Provider', 'provider')->searchable(),
            Column::make('Status', 'status'),
            Column::make('Added On', 'created_at_formatted'),
            Column::action('Action')
        ];
    }

    public function filters(): array
    {
        return [
           // Filter::inputText('client_secret')->operators(['contains']),
        ];
    }
    public function relationSearch(): array
    {
        return [];
    }
    public function setUp(): array
    {
        // $this->showCheckBox();
        return [
            Header::make()->showSearchInput(),
            Footer::make()
                ->showPerPage()
                ->showRecordCount(),
        ];
    }

    #[\Livewire\Attributes\On('edit')]

    public function actions(EmailDomain $row): array
    {

        return [
            Button::add('custom')
                ->render(function ($row) {
                    return Blade::render('
                <div class="dropdown d-inline-block">
                    <button class="btn btn-soft-secondary btn-sm dropdown" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="ri-more-2-fill align-middle"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end" style="">
                        <li><a href="javascript:void(0)" class="dropdown-item" wire:click="$dispatch(\'viewDomain\', {domainId: ' . $row->id . '})"><i class="ri-eye-fill align-bottom me-2 text-muted" ></i>View domains</a></li>

                        <li><a href="javascript:void(0)" class="dropdown-item" wire:click="$dispatch(\'domainEdit\', {domainId: ' . $row->id . '})"><i class="ri-pencil-fill align-bottom me-2 text-muted" ></i>Edit Domain</a></li>

                        @if($row->status=="Active")
                        <li>
                        <a href="javascript:void(0)" class="dropdown-item remove-item-btn" style="cursor:pointer" wire:click="$dispatch(\'domainDeactive\', {domainId: ' . $row->id . '})">
                        <i class="ri-close-circle-fill align-bottom me-2 text-danger"></i>

                            Deactive
                            </a>
                        </li>
                    @else
                    <li>
                    <a class="dropdown-item remove-item-btn" style="cursor:pointer" wire:click="$dispatch(\'domainActive\', {domainId: ' . $row->id . '})">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-check" viewBox="0 0 16 16">
                        <path d="M10.97 4.97a.75.75 0 0 1 1.07 1.05l-3.99 4.99a.75.75 0 0 1-1.08.02L4.324 8.384a.75.75 0 1 1 1.06-1.06l2.094 2.093 3.473-4.425z" class="text-success" style="font-size:20px"/>
                        </svg>
                        Activate
                        </a>
                    </li>
                        @endif

                        <li><a href="javascript:void(0)" class="dropdown-item remove-item-btn" wire:click="$dispatch(\'delete\', {domainId: ' . $row->id . '})"><i class="ri-delete-bin-fill align-bottom me-2 text-muted"></i>Delete</a></li>

                    </ul>
                </div>', compact('row'));
                }),
            ];
    }
    public function actionRules($row): array
    {
        return [
            Rule::button('edit')
                ->when(fn($row) => $row->id === 1)
                ->hide(),
        ];
    }
}
