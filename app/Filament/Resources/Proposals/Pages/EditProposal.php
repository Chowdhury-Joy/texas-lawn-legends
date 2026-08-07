<?php

namespace App\Filament\Resources\Proposals\Pages;

use App\Filament\Concerns\HasPrimarySaveAndDangerDelete;
use App\Filament\Resources\Proposals\ProposalResource;
use Filament\Resources\Pages\EditRecord;

class EditProposal extends EditRecord
{
    use HasPrimarySaveAndDangerDelete;

    protected static string $resource = ProposalResource::class;
}
