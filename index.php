<h1 class="center">Get Rate</h1>

<p>
    Це курсова работа студента комп'ютерної академії ШАГ. Мета проєкта 
    - дозволити користувачам розраховувати ставки перевезень онлайн. 
    Для продовження Вам потрібно авторізуватися.
</p>

<div class="card-panel cyan darken-1">
    <button class="btn wave-effect waves-light" onclick="getClick()">CREATE</button>
    <div id="api-result"></div>
</div>


<script>
function getClick() {
    fetch("/auth")
    .then(r => r.text())
    .then(t => {
        document.getElementById("api-result").innerText = t;
    })
}
</script>