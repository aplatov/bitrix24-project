<?php

namespace App\Models\Lists;

use App\Models\Lists\DoctorsPropertyValuesTable as DocTable;
use App\Models\Lists\ProcedurePropertyValuesTable;

class DoctorsTable extends DoctorsPropertyValuesTable
{
  /**
   * Возвращает список всех врачей вместе с процедурами.
   *
   * @return array
   */
  public static function getAllWithProcedures(): array
  {
    $doctors = DocTable::getList([
      'select' => [
        'ID' => 'IBLOCK_ELEMENT_ID',
        'NAME' => 'ELEMENT.NAME',
        'CODE' => 'ELEMENT.CODE',
        'FIRST_NAME',
        'LAST_NAME',
        'MIDDLE_NAME',
        'PROCEDURA',

      ],
    ])->fetchAll();

    $procedureIds = [];

    foreach ($doctors as $doctor) {
      foreach ((array)$doctor['PROCEDURA'] as $procedureId) {
        $procedureIds[] = (int)$procedureId;
      }
    }

    $procedureIds = array_unique($procedureIds);

    $procedures = [];

    if ($procedureIds) {
      $procedureRows = ProcedurePropertyValuesTable::getList([
        'select' => [
          'ID' => 'IBLOCK_ELEMENT_ID',
          'NAME' => 'ELEMENT.NAME',
        ],
        'filter' => [
          '@IBLOCK_ELEMENT_ID' => $procedureIds,
        ],
      ])->fetchAll();

      foreach ($procedureRows as $procedure) {
        $procedures[$procedure['ID']] = $procedure['NAME'];
      }
    }

    foreach ($doctors as &$doctor) {
      $doctor['PROCEDURES'] = [];

      foreach ((array)$doctor['PROCEDURA'] as $procedureId) {
        if (isset($procedures[$procedureId])) {
          $doctor['PROCEDURES'][] = [
            'ID' => $procedureId,
            'NAME' => $procedures[$procedureId],
          ];
        }
      }
    }

    unset($doctor);



    return $doctors;
  }
  /**
   * Возвращает информацию о враче по ID или символьному коду.
   *
   * @param int|string $doctorIdOrCode ID или символьный код врача.
   *
   * @return array
   */
  public static function getByIdOrCode(int|string $doctorIdOrCode): array
  {
    $filter = [];

    if (is_int($doctorIdOrCode) || ctype_digit((string)$doctorIdOrCode)) {
      $filter['=IBLOCK_ELEMENT_ID'] = (int)$doctorIdOrCode;
    } else {
      $filter['=ELEMENT.CODE'] = $doctorIdOrCode;
    }

    $doctor = DocTable::getList([
      'select' => [
        'ID' => 'IBLOCK_ELEMENT_ID',
        'NAME' => 'ELEMENT.NAME',
        'CODE' => 'ELEMENT.CODE',
        'FIRST_NAME',
        'LAST_NAME',
        'MIDDLE_NAME',
        'PROCEDURA',
      ],
      'filter' => $filter,
      'limit' => 1,
    ])->fetch();

    if (!$doctor) {
      return [];
    }

    $procedureIds = array_map(
      'intval',
      (array)$doctor['PROCEDURA']
    );

    $doctor['PROCEDURES'] = [];

    if (!$procedureIds) {
      return $doctor;
    }

    $procedureRows = ProcedurePropertyValuesTable::getList([
      'select' => [
        'ID' => 'IBLOCK_ELEMENT_ID',
        'NAME' => 'ELEMENT.NAME',
      ],
      'filter' => [
        '@IBLOCK_ELEMENT_ID' => $procedureIds,
      ],
    ])->fetchAll();

    $procedures = [];

    foreach ($procedureRows as $procedure) {
      $procedures[(int)$procedure['ID']] = $procedure['NAME'];
    }

    foreach ($procedureIds as $procedureId) {
      if (!isset($procedures[$procedureId])) {
        continue;
      }

      $doctor['PROCEDURES'][] = [
        'ID' => $procedureId,
        'NAME' => $procedures[$procedureId],
      ];
    }

    return $doctor;
  }
  /**
   * Возвращает список всех процедур.
   *
   * @return array Массив процедур с ID и названием.
   */
  public static function getAllProcedures(): array
  {
    return ProcedurePropertyValuesTable::getList([
      'select' => [
        'ID' => 'IBLOCK_ELEMENT_ID',
        'NAME' => 'ELEMENT.NAME',
      ],
      'order' => [
        'ELEMENT.NAME' => 'ASC',
      ],
    ])->fetchAll();
  }
  /**
   * Создает нового врача.
   *
   * @param string $name Название элемента.
   * @param string $firstName Имя.
   * @param string $lastName Фамилия.
   * @param string $middleName Отчество.
   * @param array $procedures Массив ID процедур.
   *
   * @return int ID созданного врача.
   *
   * @throws \RuntimeException
   */

  // public static function createDoctor(array $data): int
  // {
  //   $element = new \CIBlockElement();

  //   $procedures = array_values(
  //     array_unique(
  //       array_filter(
  //         array_map(
  //           'intval',
  //           (array)($data['PROCEDURA'] ?? [])
  //         )
  //       )
  //     )
  //   );

  //   $fields = [
  //     'IBLOCK_ID' => static::IBLOCK_ID,
  //     'NAME' => (string)($data['NAME'] ?? ''),
  //     'ACTIVE' => 'Y',

  //     'PROPERTY_VALUES' => [
  //       'FIRST_NAME' => (string)($data['FIRST_NAME'] ?? ''),
  //       'LAST_NAME' => (string)($data['LAST_NAME'] ?? ''),
  //       'MIDDLE_NAME' => (string)($data['MIDDLE_NAME'] ?? ''),
  //       'PROCEDURA' => $procedures,
  //     ],
  //   ];

  //   $doctorId = $element->Add($fields);

  //   if (!$doctorId) {
  //     throw new \RuntimeException(
  //       'Ошибка создания врача: ' . $element->LAST_ERROR
  //     );
  //   }

  //   return (int)$doctorId;
  // }
  /**
   * Создает нового врача.
   *
   * @param array $data Данные врача.
   *
   * @return int ID созданного врача.
   *
   * @throws \RuntimeException
   */
  public static function createDoctor(array $data): int
  {
    $element = new \CIBlockElement();

    $name = trim((string)($data['NAME'] ?? ''));

    if ($name === '') {
      throw new \RuntimeException('Не указано имя элемента.');
    }

    $procedures = array_values(
      array_unique(
        array_filter(
          array_map(
            'intval',
            (array)($data['PROCEDURA'] ?? [])
          )
        )
      )
    );

    $code = \CUtil::translit(
      $name,
      'ru',
      [
        'replace_space' => '-',
        'replace_other' => '-',
        'delete_repeat_replace' => true,
        'use_google' => false,
      ]
    );

    $fields = [
      'IBLOCK_ID' => static::IBLOCK_ID,
      'NAME' => $name,
      'CODE' => $code,
      'ACTIVE' => 'Y',

      'PROPERTY_VALUES' => [
        'FIRST_NAME' => (string)($data['FIRST_NAME'] ?? ''),
        'LAST_NAME' => (string)($data['LAST_NAME'] ?? ''),
        'MIDDLE_NAME' => (string)($data['MIDDLE_NAME'] ?? ''),
        'PROCEDURA' => $procedures,
      ],
    ];

    $doctorId = $element->Add($fields);

    if (!$doctorId) {
      throw new \RuntimeException(
        'Ошибка создания врача: ' . $element->LAST_ERROR
      );
    }

    return (int)$doctorId;
  }

  /**
   * Обновляет данные врача.
   *
   * @param int $doctorId ID врача.
   * @param array $data Данные врача.
   *
   * @return bool
   *
   * @throws \RuntimeException
   */
  public static function updateDoctor(int $doctorId, array $data): bool
  {
    $element = new \CIBlockElement();

    $fields = [];

    if (array_key_exists('NAME', $data)) {
      $name = trim((string)$data['NAME']);

      if ($name === '') {
        throw new \RuntimeException('Название врача не может быть пустым.');
      }

      $fields['NAME'] = $name;

      $fields['CODE'] = \CUtil::translit(
        $name,
        'ru',
        [
          'replace_space' => '-',
          'replace_other' => '-',
          'delete_repeat_replace' => true,
          'use_google' => false,
        ]
      );
    }

    if ($fields) {
      $result = $element->Update($doctorId, $fields);

      if (!$result) {
        throw new \RuntimeException(
          'Ошибка обновления врача: ' . $element->LAST_ERROR
        );
      }
    }

    $properties = [];

    if (array_key_exists('FIRST_NAME', $data)) {
      $properties['FIRST_NAME'] = (string)$data['FIRST_NAME'];
    }

    if (array_key_exists('LAST_NAME', $data)) {
      $properties['LAST_NAME'] = (string)$data['LAST_NAME'];
    }

    if (array_key_exists('MIDDLE_NAME', $data)) {
      $properties['MIDDLE_NAME'] = (string)$data['MIDDLE_NAME'];
    }

    if (array_key_exists('PROCEDURA', $data)) {
      $properties['PROCEDURA'] = array_values(
        array_unique(
          array_filter(
            array_map(
              'intval',
              (array)$data['PROCEDURA']
            )
          )
        )
      );
    }

    if ($properties) {
      \CIBlockElement::SetPropertyValuesEx(
        $doctorId,
        static::IBLOCK_ID,
        $properties
      );
    }

    return true;
  }
}
