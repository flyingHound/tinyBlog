<section class="container thankyou-section">
    <div class="card">
        <div class="card-body text-center">
            <h1 class="card-title">Thank You</h1>
            <?php if (flashdata()): ?>
                <div class="alert alert-success" role="alert">
                    <?= flashdata() ?>
                </div>
            <?php endif; ?>
            <p class="card-text">Your message has been successfully submitted. Our team of highly trained aardvarks are on the case.</p>
            <p class="card-text">You can expect to receive a response within 24 hours.</p>
            <p class="card-text">PLEASE NOTE - That doesn't necessarily mean you'll actually <i>get</i> a response within 24 hours, but at least you can <i>expect</i> one!</p>
            <p class="card-text">Cheers!</p>
            <p class="card-text"><?= WEBSITE_NAME ?> Support</p>
            <a href="<?= BASE_URL ?>" class="btn btn-primary">Back to Home</a>
        </div>
    </div>
</section>

<style>
    .thankyou-section {
        padding: 50px 0;
        max-width: 600px;
        margin: 0 auto;
    }
</style>