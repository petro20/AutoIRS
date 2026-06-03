/* AutoIRS — JavaScript do frontend */

document.addEventListener('DOMContentLoaded', function () {
    // Fecha automaticamente os alertas flash após 5 segundos.
    document.querySelectorAll('.alert-dismissible').forEach(function (alerta) {
        setTimeout(function () {
            const instancia = bootstrap.Alert.getOrCreateInstance(alerta);
            instancia.close();
        }, 5000);
    });

    // Formata campos de IBAN em grupos de 4 caracteres enquanto se escreve.
    const iban = document.querySelector('input[name="iban"]');
    if (iban) {
        iban.addEventListener('input', function () {
            let v = this.value.replace(/\s+/g, '').toUpperCase();
            this.value = v.replace(/(.{4})/g, '$1 ').trim();
        });
    }
});
