<?php

namespace Core\Instacover\Models;

/**
 * Class Poptavka
 *
 * Represents a model for handling "Poptavka" (inquiry/request) data within the application.
 * Provides functionality for managing and interacting with inquiry records.
 *
 * @package App\Models
 */
class Poptavka
{
    private ?int $id = null;

    private ?array $poptavka = null;

    private string $table;

    /**
     * Summary of __construct
     * @param int $id
     * @param string $table
     */
    public function __construct(int $id, string $table)
    {
        $this->id = $id;
        $this->table = $table;
        $this->poptavka = $this->loadRow();
    }

    /**
     * Summary of loadRow
     * @return array
     */
    private function loadRow(): ?array
    {
        $sql = coreDBSel("SELECT * FROM {$this->table} WHERE id = ?", [$this->id]);

        if ($sql && $sql->recordCount() > 0) {
            return $sql->fetchRow();
        } else {
            return null;
        }
    }

    /**
     * Summary of saveSesionId
     * @param string $sessionId
     * @return bool
     */
    public function saveSesionId(string $sessionId): bool
    {
        if (!$this->poptavka) {
            return false;
        }

        $edit = ['instacover_session_id' => ['value' => $sessionId]];
        $sql = coreDBEdit($this->table,$edit,"(id = '".$this->id."')",[],1,true);
        return $sql ? true : false;
    }

    /**
     * Summary of getPoptavkaSessionId
     * @rreturn string|null
     */
    public function getPoptavkaSessionId(): ?string
    {
        return $this->poptavka['instacover_session_id'] ?? null;
    }
}