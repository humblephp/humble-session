<?php

namespace Humble\Session;

class PdoSessionHandler implements \SessionHandlerInterface
{
    private $pdo;

    private $settings = [
        'tableName' => 'sessions',
        'idField' => 'id',
        'timeField' => 'time',
        'dataField' => 'data',
    ];

    public function __construct(\Pdo $pdo, array $settings = array())
    {
        $this->pdo = $pdo;
        $this->settings = array_merge($this->settings, $settings);
    }

    public function close()
    {
        return true;
    }

    public function destroy($sessionId)
    {
        $sql = vsprintf('DELETE FROM %s WHERE %s = :id', [
            $this->settings['tableName'],
            $this->settings['idField']
        ]);

        $query = $this->pdo->prepare($sql);
        $query->bindValue(':id', $sessionId);

        return $query->execute();
    }

    /**
     * @SuppressWarnings(PHPMD.ShortMethodName)
     */
    public function gc($lifetime)
    {
        $sql = vsprintf('DELETE FROM %s WHERE %s < :time', [
            $this->settings['tableName'],
            $this->settings['timeField']
        ]);

        $query = $this->pdo->prepare($sql);
        $query->bindValue(':time', time() - $lifetime);

        return $query->execute();
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function open($savePath, $sessionName)
    {
        return $this->pdo ? true : false;
    }

    public function read($sessionId)
    {
        $sql = vsprintf('SELECT %s FROM %s WHERE %s = :id', [
            $this->settings['dataField'],
            $this->settings['tableName'],
            $this->settings['idField']
        ]);

        $query = $this->pdo->prepare($sql);
        $query->bindValue(':id', $sessionId);
        $query->execute();

        return (string) $query->fetchColumn();
    }

    public function write($sessionId, $sessionData)
    {
        $sql = vsprintf('REPLACE INTO %s VALUES (:id, :time, :data)', [
            $this->settings['tableName']
        ]);

        $query = $this->pdo->prepare($sql);
        $query->bindValue(':id', $sessionId);
        $query->bindValue(':time', time());
        $query->bindValue(':data', $sessionData);

        return $query->execute();
    }
}
