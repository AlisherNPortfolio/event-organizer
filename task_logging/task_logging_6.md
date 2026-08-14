# Task Logging 6 — Cron skripti va Crontab formatidagi runtime xatolarini tuzatish

## Vazifa

`task_logging_5.md`da Docker build xatosini tuzatgandan so'ng, image ichida `crond`ni haqiqatan sinab ko'rganimda ikkita **runtime** (build'dan keyingi, ishga tushirish vaqtidagi) xato topilgan edi:
1. `_container_data/cron/Crontab` faylida cron vaqt maydonlaridan biri (hafta kuni) tushib qolgan — `crond` uni parslay olmayapti.
2. `scripts/run-scheduler-cron.sh` fayli butunlay bo'sh (0 bayt).

Foydalanuvchi tasdiqladi: skript `php artisan schedule:run` buyrug'ini ishga tushirishi kerak, va ikkalasini ham tuzatishni so'radi.

## 1-qadam: Joriy holatni qayta tekshirish

**Buyruq:** `ls -la scripts/` va faylni hex ko'rinishida tekshirish (`xxd`)

**Natija:** `scripts/run-scheduler-cron.sh` — 0 bayt, chindan ham butunlay bo'sh ekani tasdiqlandi.

**Buyruq:** `grep -in "bash\|ash\|shell" _container_data/dockerfiles/php.dockerfile`

**Nima uchun:** Skript qanday shell interpretatoriga mo'ljallanishi kerakligini aniqlash uchun. Baza image `php:8.4-fpm-alpine` bo'lib, u Alpine Linux'ga asoslangan — standart holatda faqat BusyBox'ning `ash`/`sh`i mavjud, `bash` alohida o'rnatilmaguncha yo'q. Dockerfile'da hech qanday joyda `bash` o'rnatilmaganini tasdiqladim (natija bo'sh).

**Xulosa:** Skript **POSIX `sh`** sintaksisida yozilishi kerak (`#!/bin/sh`), `bash`ga xos xususiyatlardan (masalan, `[[ ]]`, massivlar) foydalanmasdan — aks holda konteyner ichida "not found" yoki sintaksis xatosi berishi mumkin edi.

## 2-qadam: `scripts/run-scheduler-cron.sh` skriptini yozish

**Vosita:** `Write`
**Fayl:** `scripts/run-scheduler-cron.sh`

**Yozilgan mazmun:**
```sh
#!/bin/sh

cd /var/www/html || exit 1

php artisan schedule:run >> /var/log/cron.log 2>&1
```

**Har bir qatorning sababi:**
- `#!/bin/sh` — Alpine image'da mavjud bo'lgan POSIX shell'ni ko'rsatadi (1-qadamda tasdiqlangan).
- `cd /var/www/html || exit 1` — `php artisan` buyrug'i Laravel loyihasining ildiz papkasidan (u yerda `artisan` fayli joylashgan) ishga tushirilishi shart; `php.dockerfile`da `WORKDIR /var/www/html` va `COPY . .` orqali butun loyiha aynan shu manzilga joylashtirilgan (bu ilgari, upgrade vazifasi paytida tasdiqlangan). `|| exit 1` — agar papka negadir mavjud bo'lmasa, skript jimgina keyingi (noto'g'ri joydagi) buyruqni bajarishga urinmasdan, aniq xato kodi bilan to'xtaydi.
- `php artisan schedule:run` — bu Laravel'ning standart, hujjatlashtirilgan cron integratsiya buyrug'i: u `bootstrap/app.php`dagi `->withSchedule(...)` orqali ro'yxatga olingan barcha vazifalarni (hozircha faqat `events:process-status`, `task_logging_1.md`/`_4.md`da tahlil qilingan) tekshiradi va ulardan qaysi biri aynan shu daqiqada bajarilishi kerak bo'lsa, o'shani ishga tushiradi. Bu — Laravel'ning rasmiy tavsiya qilingan yagona cron yozuvi orqali butun scheduler'ni boshqarish usuli (loyihaning o'zi ham aynan shu naqshni — bitta "* * * * *" yozuvi orqali — qo'llamoqchi bo'lgan, buni `Crontab` faylining tuzilishidan ko'rish mumkin edi).
- `>> /var/log/cron.log 2>&1` — buyruqning ham oddiy chiqishi (stdout), ham xato chiqishi (stderr) `/var/log/cron.log` fayliga qo'shib yoziladi. `task_logging_5.md`ning 10-qadamida aniqlanganidek, aynan shu fayl `php.dockerfile`da `RUN ln -sf /dev/stdout /var/log/cron.log` orqali konteynerning `stdout`iga bog'langan — demak, bu yozuvlar `docker logs`/`docker compose logs`orqali ko'rinadi, log fayllari konteyner ichida cheksiz o'sib ketmaydi.

## 3-qadam: `_container_data/cron/Crontab` faylidagi formatni tuzatish

**Vosita:** `Edit`
**Fayl:** `_container_data/cron/Crontab`

**O'zgarish:**
```diff
- * * * * /var/www/html/scripts/run-scheduler-cron.sh
+ * * * * * /var/www/html/scripts/run-scheduler-cron.sh
```

**Sabab:** Standart cron format 5 ta vaqt maydonini talab qiladi: **minut, soat, oyning kuni, oy, haftaning kuni** — so'ngra bajariladigan buyruq. Avvalgi yozuvda faqat 4 ta yulduzcha bor edi (haftaning kuni maydoni tushib qolgan), shuning uchun `crond` skript yo'lini "haftaning kuni" maydoni sifatida talqin qilishga urinib, buyruqni topa olmay xato berayotgan edi (`task_logging_5.md`, 12-qadamda amalda tasdiqlangan: `failed parsing crontab for user root: /var/www/html/scripts/run-scheduler-cron.sh`). 5 ta yulduzcha (`* * * * *`) — "har daqiqa, har soat, har kun, har oy, haftaning har kunida" degani, ya'ni skript **har daqiqada bir marta** ishga tushadi, bu esa Laravel scheduler uchun rasmiy tavsiya etilgan chastota (chunki `schedule:run`ning o'zi ichki ro'yxatdagi vazifalarni ularning haqiqiy jadvaliga (`everyFifteenMinutes()` va h.k.) qarab filtrlaydi).

## 4-qadam: Tuzatishlarni Docker orqali amaliy tasdiqlash

**Buyruq:** `docker compose build php` — image qayta build qilindi (yangi skript va Crontab fayli konteynerga `COPY . .` va `COPY _container_data/cron/Crontab /etc/cron.d/` orqali joylashishi uchun).

**Buyruq:** o'rnatilgan image ichida `crond`ni qisqa vaqt ishga tushirib, endi crontab **xatosiz** parslanayotganini va (agar mumkin bo'lsa) `run-scheduler-cron.sh` skriptining o'zi xatosiz ishlayotganini tekshirish.

**Natija:**

`docker compose build php` muvaffaqiyatli yakunlandi (`Image event-organizer-php Built`).

Keyin build qilingan image ichida bir nechta tekshiruv o'tkazdim:

1. **`crond -f -d 8`ni fon rejimida ishga tushirib, 3 soniyadan keyin to'xtatish** (bu usul `timeout` buyrug'i bilan bog'liq "setpgid: Operation not permitted" degan, ushbu lokal sandbox muhitiga xos, loyihaga aloqasi yo'q texnik g'alizlikni chetlab o'tish uchun tanlandi):
   ```
   crond 4.5 dillon's cron daemon, started with loglevel debug
   Synchronizing /etc/crontabs
   ...
   Synchronizing /etc/cron.d
   User root Entry * * * * * /var/www/html/scripts/run-scheduler-cron.sh
   ...
   TestStartup for FILE /etc/cron.d/Crontab USER root:
       LINE /var/www/html/scripts/run-scheduler-cron.sh
   ```
   ✅ **Hech qanday "failed parsing" xatosi yo'q** (avval, tuzatishdan oldin aynan shu joyda `failed parsing crontab for user root: ...` xatosi chiqqan edi — `task_logging_5.md`, 12-qadam). `crond` endi yozuvni **to'g'ri 5 maydonli cron yozuvi** sifatida tan oldi va uni "root" foydalanuvchi nomiga bog'ladi.

2. **Skriptni qo'lda, konteyner ichida ishga tushirish** (`sh /var/www/html/scripts/run-scheduler-cron.sh`):
   - Skript xatosiz ishga tushdi (POSIX `sh` sintaksisi to'g'ri, `cd` muvaffaqiyatli bajarildi).
   - `php artisan schedule:run` chaqirildi va u **Laravel'ning ichki mantig'i bo'yicha to'g'ri ishladi** — buyruq `cache` jadvalidan `laravel-cache-illuminate:schedule:paused` kalitini so'rashga urindi (bu `schedule:run`ning odatiy, hujjatlashtirilgan birinchi qadami — scheduler pauza qilinmaganini tekshirish). Xato faqat shu nuqtada, **MySQL bazasiga ulanishda** chiqdi:
     ```
     SQLSTATE[HY000] [2002] php_network_getaddresses: getaddrinfo for mysql_db failed: Name does not resolve
     ```
   **Bu tuzatishdagi xato emas** — buning sababi shunchaki men skriptni **`docker compose`ning ichida emas, alohida `docker run --rm` orqali**, ya'ni `mysql_db` xizmati ishlab turgan Compose tarmog'idan tashqarida sinab ko'rganim edi. Haqiqiy joylashtirishda (`docker compose up`) barcha xizmatlar (`php`, `mysql_db` va h.k.) bitta ichki tarmoqda bo'lib, `mysql_db` nomi muammosiz DNS orqali topiladi. Muhim xulosa: **skriptning o'zi va u chaqirayotgan `php artisan schedule:run` buyrug'i to'g'ri ishlayapti** — bazaga ulanishgacha bo'lgan butun zanjir (`sh` skript → `cd` → `php artisan` → Laravel bootstrap → DB query'ga urinish) xatosiz o'tdi.

**Xulosa:** Ikkala runtime xato ham (`task_logging_5.md`da topilgan) endi tuzatildi va amalda tasdiqlandi:
- ✅ `_container_data/cron/Crontab` — endi to'g'ri 5-maydonli cron formatida, `crond` tomonidan xatosiz parslanadi.
- ✅ `scripts/run-scheduler-cron.sh` — endi bo'sh emas, `php artisan schedule:run`ni to'g'ri ishga tushiradi, natijani `/var/log/cron.log` (→ `docker logs` orqali ko'rinadigan `stdout`) ga yozadi.

Endi konteyner to'liq `docker compose up` orqali ishga tushirilganda, tadbir holatini avtomatik yangilash vazifasi (`events:process-status`, har 15 daqiqada, `bootstrap/app.php`da belgilangan) haqiqatan ham ishlashi kutiladi — buni faqat to'liq stack (`mysql_db` bilan birga) ishga tushirilganda yakuniy tasdiqlash mumkin.
