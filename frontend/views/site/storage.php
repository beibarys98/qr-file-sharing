<?php

use yii\helpers\Url as HelpersUrl;

$this->title = 'QR-drop';

$lastId = !empty($items) ? (int)$items[count($items) - 1]['id'] : 0;
$checkStorageUrl = \yii\helpers\Url::to([
    'site/check-storage',
    'token' => $token,
    'lastId' => $lastId
]);
?>
<script>
    setInterval(function() {
        fetch('<?= $checkStorageUrl ?>')
            .then(r => r.json())
            .then(d => {
                if (d.updated) {
                    location.reload();
                }
            })
            .catch(err => console.error(err));
    }, 2000);
</script>



<div class="mt-5" style="max-width:600px; margin:0 auto; display:flex; flex-direction:column; gap:12px;">

    <?php if (empty($items)): ?>
        <p style="text-align:center; color:#888;">Файлдар немесе сілтемелер әлі салынбады!</p>
    <?php else: ?>
        <?php foreach ($items as $item): ?>
            <div class="btn btn-light d-flex justify-content-between p-3" style="cursor: default;">
                <?php if ($item['type'] === 'file'): ?>
                    <span>📄 <?= basename($item['value']) ?></span>
                    <a href="/<?= $item['value'] ?>" target="_blank" class="text-success text-decoration-none fw-bold">Жүктеп алу</a>
                <?php else: ?>
                    <span>🌐 <a href="<?= $item['value'] ?>" target="_blank" style="color:#333; text-decoration:none;"><?= $item['value'] ?></a></span>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>

    <div class="text-center mt-3">
        <a class="btn btn-danger" href="<?= HelpersUrl::to(['index', 'token' => $token]) ?>">Аяқтау</a>
    </div>

</div>