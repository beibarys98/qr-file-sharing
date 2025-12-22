<?php

use yii\helpers\Html;
?>

<div class="mt-5" style="max-width:600px; margin:0 auto; display:flex; flex-direction:column; gap:12px;">

    <?php if (empty($items)): ?>
        <p style="text-align:center; color:#888;">Файлдар немесе сілтемелер әлі салынбады!</p>

        <div style="text-align:center; margin-top:10px;">
            <button
                onclick="location.reload();"
                style="
                padding:8px 16px;
                background-color:#2196F3;
                color:white;
                border:none;
                border-radius:4px;
                cursor:pointer;
                font-weight:bold;
                transition: background-color 0.3s;
            "
                onmouseover="this.style.backgroundColor='#1976D2';"
                onmouseout="this.style.backgroundColor='#2196F3';">
                Жаңарту
            </button>
        </div>
    <?php else: ?>
        <?php foreach ($items as $item): ?>
            <div style="
                padding:12px 16px;
                border:1px solid #ddd;
                border-radius:6px;
                background:#fdfdfd;
                box-shadow:0 1px 3px rgba(0,0,0,0.05);
                display:flex;
                justify-content:space-between;
                align-items:center;
                transition: box-shadow 0.3s;
            "
                onmouseover="this.style.boxShadow='0 4px 12px rgba(0,0,0,0.1)';"
                onmouseout="this.style.boxShadow='0 1px 3px rgba(0,0,0,0.05)';">
                <?php if ($item['type'] === 'file'): ?>
                    <span>📁 <?= basename($item['value']) ?></span>
                    <a href="/<?= $item['value'] ?>" target="_blank" style="
                        text-decoration:none; 
                        color:#4CAF50; 
                        font-weight:bold;
                        transition: color 0.3s;
                    "
                        onmouseover="this.style.color='#45a049';"
                        onmouseout="this.style.color='#4CAF50';">Жүктеп алу</a>
                <?php else: ?>
                    <span>🔗 <a href="<?= $item['value'] ?>" target="_blank" style="color:#333; text-decoration:none;"><?= $item['value'] ?></a></span>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
        <div style="text-align:center; margin-top:10px;">
            <button
                onclick="location.reload();"
                style="
                padding:8px 16px;
                background-color:#2196F3;
                color:white;
                border:none;
                border-radius:4px;
                cursor:pointer;
                font-weight:bold;
                transition: background-color 0.3s;
            "
                onmouseover="this.style.backgroundColor='#1976D2';"
                onmouseout="this.style.backgroundColor='#2196F3';">
                Жаңарту
            </button>
        </div>
    <?php endif; ?>

</div>