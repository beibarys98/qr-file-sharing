<div class="text-center mt-5">
    <img src="https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=<?= urlencode($qrUrl) ?>">
</div>

<script>
    setInterval(function() {
        fetch('<?= \yii\helpers\Url::to(['qr/check-scan', 'token' => $token]) ?>')
            .then(r => r.json())
            .then(d => {
                if (d.scanned) {
                    window.location.href = '<?= \yii\helpers\Url::to(['qr/storage', 'token' => $token]) ?>';
                }
            });
    }, 2000);
</script>