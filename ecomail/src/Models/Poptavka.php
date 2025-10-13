<?php

namespace Core\Ecomail\Models;

class Poptavka
{
    private ?int $id = null;

    private ?array $poptavka = null;

    private string $table;

    public function __construct(int $id, string $table)
    {
        $this->id = $id;
        $this->table = $table;
        $this->poptavka = $this->loadRow();
    }

    private function loadRow(): ?array
    {
        $sql = coreDBSel("SELECT * FROM {$this->table} WHERE id = ?", [$this->id]);

        if ($sql && $sql->recordCount() > 0) {
            return $sql->fetchRow();
        } else {
            return null;
        }
    }

    public function removeMarketingAgreement(): bool
    {
        if (!$this->poptavka) {
            return false;
        }

        $edit = ['marketing' => ['value' => 0]];
        $sql = coreDBEdit($this->table,$edit,"(id = '".$this->id."')",[],1,true);
        return $sql ? true : false;
    }
}