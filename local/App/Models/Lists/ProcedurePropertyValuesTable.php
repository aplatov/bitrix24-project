<?php

namespace App\Models\Lists;

use App\Models\AbstractIblockPropertyValuesTable;
use Bitrix\Iblock\ElementTable;
use Bitrix\Main\Entity\ReferenceField;

class ProcedurePropertyValuesTable extends AbstractIblockPropertyValuesTable
{
    public const IBLOCK_ID = 17;

    public static function getMap(): array
    {
        return parent::getMap() + [
            'ELEMENT' => new ReferenceField(
                'ELEMENT',
                ElementTable::class,
                [
                    '=this.IBLOCK_ELEMENT_ID' => 'ref.ID',
                ]
            ),
        ];
    }
    public static function getAllWithProcedures(): array
    {
        $doctors = static::getList([
            'select' => [
                'ID' => 'IBLOCK_ELEMENT_ID',
                'NAME' => 'ELEMENT.NAME',
                'FIRST_NAME',
                'LAST_NAME',
                'MIDDLE_NAME',
                'PROCEDURA',
                'PROCEDURA_ELEMENT_NAME',
            ],
            'order' => [
                'ELEMENT.NAME' => 'ASC',
            ],
        ])->fetchAll();

        foreach ($doctors as &$doctor) {
            $procedureIds = (array)($doctor['PROCEDURA'] ?? []);
            $procedureNames = (array)($doctor['PROCEDURA_ELEMENT_NAME'] ?? []);

            $doctor['PROCEDURES'] = [];

            foreach ($procedureIds as $index => $procedureId) {
                $doctor['PROCEDURES'][] = [
                    'ID' => (int)$procedureId,
                    'NAME' => $procedureNames[$index] ?? '',
                ];
            }

            unset(
                $doctor['PROCEDURA'],
                $doctor['PROCEDURA_ELEMENT_NAME']
            );
        }

        unset($doctor);

        return $doctors;
    }
}
