<?php

require $_SERVER['DOCUMENT_ROOT'] . '/bitrix/header.php';



use App\Models\Lists\DoctorsTable;

global $APPLICATION;

$APPLICATION->SetTitle('Врачи');

/**
 * Экранирует значение для HTML.
 *
 * @param mixed $value Значение.
 *
 * @return string
 */
function h(mixed $value): string
{
  return htmlspecialchars(
    (string)$value,
    ENT_QUOTES | ENT_SUBSTITUTE,
    'UTF-8'
  );
}

$action = (string)($_GET['action'] ?? '');
$doctorId = (int)($_GET['id'] ?? 0);

$error = '';
$success = '';

/*
 * Обработка формы.
 */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (!check_bitrix_sessid()) {
    $error = 'Ошибка проверки сессии.';
  } else {
    $formAction = (string)($_POST['form_action'] ?? '');

    $data = [
      'NAME' => trim((string)($_POST['NAME'] ?? '')),
      'FIRST_NAME' => trim((string)($_POST['FIRST_NAME'] ?? '')),
      'LAST_NAME' => trim((string)($_POST['LAST_NAME'] ?? '')),
      'MIDDLE_NAME' => trim((string)($_POST['MIDDLE_NAME'] ?? '')),
      'PROCEDURA' => array_map(
        'intval',
        (array)($_POST['PROCEDURA'] ?? [])
      ),
    ];

    try {
      /*
             * Добавление врача.
             */
      if ($formAction === 'add') {
        $newDoctorId = DoctorsTable::createDoctor($data);

        LocalRedirect(
          '?action=edit&id=' . $newDoctorId . '&saved=Y'
        );
      }

      /*
             * Редактирование врача.
             */
      if ($formAction === 'edit') {
        $editDoctorId = (int)($_POST['ID'] ?? 0);

        if ($editDoctorId <= 0) {
          throw new RuntimeException('Не указан ID врача.');
        }

        DoctorsTable::updateDoctor(
          $editDoctorId,
          $data
        );

        LocalRedirect(
          '?action=edit&id=' . $editDoctorId . '&saved=Y'
        );
      }
    } catch (Throwable $exception) {
      $error = $exception->getMessage();
    }
  }
}

if (($_GET['saved'] ?? '') === 'Y') {
  $success = 'Данные успешно сохранены.';
}

/*
 * Получаем список всех процедур.
 */
$procedures = DoctorsTable::getAllProcedures();

?>

<style>
  .doctors-page {
    max-width: 900px;
    margin: 30px auto;
    font-family: Arial, sans-serif;
  }

  .doctors-page h1 {
    margin-bottom: 25px;
  }

  .doctors-list {
    margin: 0;
    padding: 0;
    list-style: none;
    border: 1px solid #ddd;
    border-radius: 6px;
    overflow: hidden;
  }

  .doctors-list__item {
    border-bottom: 1px solid #ddd;
  }

  .doctors-list__item:last-child {
    border-bottom: 0;
  }

  .doctors-list__link {
    display: block;
    padding: 15px 20px;
    color: #222;
    text-decoration: none;
    transition: background .2s;
  }

  .doctors-list__link:hover {
    background: #f5f5f5;
  }

  .doctors-list__name {
    font-weight: 600;
    margin-bottom: 5px;
  }

  .doctors-list__procedures {
    font-size: 13px;
    color: #777;
  }

  .doctor-form {
    border: 1px solid #ddd;
    border-radius: 6px;
    padding: 25px;
  }

  .doctor-form__row {
    margin-bottom: 20px;
  }

  .doctor-form__label {
    display: block;
    margin-bottom: 7px;
    font-weight: 600;
  }

  .doctor-form__input {
    box-sizing: border-box;
    width: 100%;
    height: 42px;
    padding: 7px 10px;
    border: 1px solid #ccc;
    border-radius: 4px;
    font-size: 14px;
  }

  .doctor-form__procedures {
    display: flex;
    flex-direction: column;
    gap: 8px;
  }

  .doctor-form__procedure {
    display: flex;
    align-items: center;
    gap: 8px;
  }

  .doctor-form__buttons {
    display: flex;
    gap: 10px;
    margin-top: 25px;
  }

  .button {
    display: inline-block;
    padding: 10px 18px;
    border: 0;
    border-radius: 4px;
    background: #1677ff;
    color: #fff;
    text-decoration: none;
    cursor: pointer;
    font-size: 14px;
  }

  .button:hover {
    opacity: .9;
  }

  .button--secondary {
    background: #777;
  }

  .button--add {
    margin-top: 20px;
    background: #28a745;
  }

  .message {
    padding: 12px 15px;
    margin-bottom: 20px;
    border-radius: 4px;
  }

  .message--error {
    background: #ffe5e5;
    color: #b00000;
  }

  .message--success {
    background: #e5f7e8;
    color: #167326;
  }
</style>

<div class="doctors-page">

  <?php if ($error !== ''): ?>
    <div class="message message--error">
      <?= h($error) ?>
    </div>
  <?php endif; ?>

  <?php if ($success !== ''): ?>
    <div class="message message--success">
      <?= h($success) ?>
    </div>
  <?php endif; ?>


  <?php
  /*
     * Страница добавления врача.
     */
  if ($action === 'add'):
  ?>

    <h1>Добавление врача</h1>

    <form
      method="post"
      class="doctor-form">
      <?= bitrix_sessid_post() ?>

      <input
        type="hidden"
        name="form_action"
        value="add">

      <div class="doctor-form__row">
        <label class="doctor-form__label">
          Название
        </label>

        <input
          type="text"
          name="NAME"
          class="doctor-form__input"
          value="<?= h($_POST['NAME'] ?? '') ?>"
          required>
      </div>

      <div class="doctor-form__row">
        <label class="doctor-form__label">
          Имя
        </label>

        <input
          type="text"
          name="FIRST_NAME"
          class="doctor-form__input"
          value="<?= h($_POST['FIRST_NAME'] ?? '') ?>">
      </div>

      <div class="doctor-form__row">
        <label class="doctor-form__label">
          Фамилия
        </label>

        <input
          type="text"
          name="LAST_NAME"
          class="doctor-form__input"
          value="<?= h($_POST['LAST_NAME'] ?? '') ?>">
      </div>

      <div class="doctor-form__row">
        <label class="doctor-form__label">
          Отчество
        </label>

        <input
          type="text"
          name="MIDDLE_NAME"
          class="doctor-form__input"
          value="<?= h($_POST['MIDDLE_NAME'] ?? '') ?>">
      </div>

      <div class="doctor-form__row">

        <div class="doctor-form__label">
          Процедуры
        </div>

        <div class="doctor-form__procedures">

          <?php foreach ($procedures as $procedure): ?>

            <?php
            $procedureId = (int)$procedure['ID'];

            $checked = in_array(
              $procedureId,
              array_map(
                'intval',
                (array)($_POST['PROCEDURA'] ?? [])
              ),
              true
            );
            ?>

            <label class="doctor-form__procedure">

              <input
                type="checkbox"
                name="PROCEDURA[]"
                value="<?= $procedureId ?>"
                <?= $checked ? 'checked' : '' ?>>

              <span>
                <?= h($procedure['NAME']) ?>
              </span>

            </label>

          <?php endforeach; ?>

        </div>

      </div>

      <div class="doctor-form__buttons">

        <button
          type="submit"
          class="button">
          Добавить
        </button>

        <a
          href="?"
          class="button button--secondary">
          Отмена
        </a>

      </div>

    </form>


    <?php
  /*
     * Страница редактирования врача.
     */
  elseif ($action === 'edit' && $doctorId > 0):

    $doctor = DoctorsTable::getByIdOrCode($doctorId);

    if (!$doctor):
    ?>

      <div class="message message--error">
        Врач не найден.
      </div>

      <a
        href="?"
        class="button button--secondary">
        Вернуться к списку
      </a>

    <?php else: ?>

      <h1>
        Редактирование врача:
        <?= h($doctor['NAME']) ?>
      </h1>

      <form
        method="post"
        class="doctor-form">
        <?= bitrix_sessid_post() ?>

        <input
          type="hidden"
          name="form_action"
          value="edit">

        <input
          type="hidden"
          name="ID"
          value="<?= (int)$doctor['ID'] ?>">

        <div class="doctor-form__row">
          <label class="doctor-form__label">
            Название
          </label>

          <input
            type="text"
            name="NAME"
            class="doctor-form__input"
            value="<?= h($doctor['NAME']) ?>"
            required>
        </div>

        <div class="doctor-form__row">
          <label class="doctor-form__label">
            Имя
          </label>

          <input
            type="text"
            name="FIRST_NAME"
            class="doctor-form__input"
            value="<?= h($doctor['FIRST_NAME']) ?>">
        </div>

        <div class="doctor-form__row">
          <label class="doctor-form__label">
            Фамилия
          </label>

          <input
            type="text"
            name="LAST_NAME"
            class="doctor-form__input"
            value="<?= h($doctor['LAST_NAME']) ?>">
        </div>

        <div class="doctor-form__row">
          <label class="doctor-form__label">
            Отчество
          </label>

          <input
            type="text"
            name="MIDDLE_NAME"
            class="doctor-form__input"
            value="<?= h($doctor['MIDDLE_NAME']) ?>">
        </div>

        <div class="doctor-form__row">

          <div class="doctor-form__label">
            Процедуры
          </div>

          <div class="doctor-form__procedures">

            <?php
            $selectedProcedures = array_map(
              'intval',
              (array)($doctor['PROCEDURA'] ?? [])
            );
            ?>

            <?php foreach ($procedures as $procedure): ?>

              <?php
              $procedureId = (int)$procedure['ID'];

              $checked = in_array(
                $procedureId,
                $selectedProcedures,
                true
              );
              ?>

              <label class="doctor-form__procedure">

                <input
                  type="checkbox"
                  name="PROCEDURA[]"
                  value="<?= $procedureId ?>"
                  <?= $checked ? 'checked' : '' ?>>

                <span>
                  <?= h($procedure['NAME']) ?>
                </span>

              </label>

            <?php endforeach; ?>

          </div>

        </div>

        <div class="doctor-form__buttons">

          <button
            type="submit"
            class="button">
            Сохранить
          </button>

          <a
            href="?"
            class="button button--secondary">
            Назад
          </a>

        </div>

      </form>

    <?php endif; ?>


  <?php
  /*
     * Основная страница — список врачей.
     */
  else:

    $doctors = DoctorsTable::getAllWithProcedures();
  ?>

    <h1>Врачи</h1>
    <h2 class="mb-3">Разработка простого приложения для работы со списками на D7</h2>
    <p>Репозиторий :<a href="https://github.com/aplatov/bitrix24-project/tree/main"> https://github.com/aplatov/bitrix24-project/tree/main</a></p>
    <BR>

    <?php if ($doctors): ?>

      <ul class="doctors-list">

        <?php foreach ($doctors as $doctor): ?>

          <li class="doctors-list__item">

            <a
              href="?action=edit&id=<?= (int)$doctor['ID'] ?>"
              class="doctors-list__link">

              <div class="doctors-list__name">
                <?= h($doctor['NAME']) ?>
              </div>

              <?php if (!empty($doctor['PROCEDURES'])): ?>

                <div class="doctors-list__procedures">

                  <?php
                  $procedureNames = array_column(
                    $doctor['PROCEDURES'],
                    'NAME'
                  );
                  ?>

                  <?= h(
                    implode(
                      ', ',
                      $procedureNames
                    )
                  ) ?>

                </div>

              <?php endif; ?>

            </a>

          </li>

        <?php endforeach; ?>

      </ul>

    <?php else: ?>

      <p>
        Врачи пока не добавлены.
      </p>

    <?php endif; ?>

    <a
      href="?action=add"
      class="button button--add">
      Добавить врача
    </a>

  <?php endif; ?>

</div>

<?php

require $_SERVER['DOCUMENT_ROOT'] . '/bitrix/footer.php';
