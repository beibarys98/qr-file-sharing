<?php

use yii\helpers\Html;

$this->title = 'QR-drop';
?>

<div class="mt-5" style="max-width:400px; margin:0 auto; padding:20px; border:1px solid #ddd; border-radius:8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); background:#f9f9f9;">

    <form method="post" enctype="multipart/form-data" style="display:flex; flex-direction:column; gap:15px;">
        <?= Html::hiddenInput(Yii::$app->request->csrfParam, Yii::$app->request->csrfToken) ?>

        <label style="font-weight:bold;">Файл Таңдаңыз:</label>
        <input type="file" name="file" style="padding:8px; border-radius:4px; border:1px solid #ccc;">

        <label style="font-weight:bold;">Немесе Сілтеме Салыңыз:</label>
        <input type="text" name="link" style="padding:8px; border-radius:4px; border:1px solid #ccc;">

        <button type="submit" class="btn btn-success fw-bold">
            Салу
        </button>
    </form>

</div>

<script>
    const token = '<?= $token ?>'; // pass token from server to view

    const interval = setInterval(() => {
        fetch('<?= \yii\helpers\Url::to(['site/check-token']) ?>?token=' + token)
            .then(res => res.json())
            .then(data => {
                if (data.deleted) {
                    clearInterval(interval);
                    window.location.href = '<?= \yii\helpers\Url::to(['site/thanks']) ?>';
                }
            });
    }, 2000); // check every 2 seconds
</script>