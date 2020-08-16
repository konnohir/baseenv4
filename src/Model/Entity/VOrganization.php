<?php

declare(strict_types=1);

namespace App\Model\Entity;

/**
 * VOrganization Entity
 */
class VOrganization extends AppEntity
{

    /**
     * レコードを一意に識別するIDを取得する
     * @return string ID (本部ID、部店ID、課IDをスラッシュ区切りにした値)
     */
    public function getId()
    {
        return implode('/', array_filter([$this->m_department1_id, $this->m_department2_id, $this->m_department3_id], function ($v) {
            return $v !== null;
        }));
    }
}
