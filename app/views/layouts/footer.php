    </main>

<footer class="py-4 mt-5">
    <div class="container text-center text-muted small">
        <i class="bi bi-gem" style="color:var(--gold)"></i>
        &copy; <?= date('Y') ?> <strong style="color:var(--gold-2);font-family:'Playfair Display',serif"><?= APP_NAME ?></strong>
        — <?= e(t('footer.tagline')) ?>
        <br>
        <span class="text-danger"><?= e(t('footer.disclaimer')) ?></span>
    </div>
</footer>

<!-- Bootstrap 5 JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="<?= url('assets/js/app.js') ?>"></script>
</body>
</html>
