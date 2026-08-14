# Task Logging 5 — Docker build xatoligini tashxislash va tuzatish

## Vazifa

Foydalanuvchi `docker compose build` (yoki shunga o'xshash) buyrug'ini ishga tushirganda quyidagi xato bilan duch keldi:

```
php.dockerfile:21
>>> COPY /var/www/html/_container_data/cron/Crontab /etc/cron.d/
failed to solve: failed to compute cache key: failed to calculate checksum of ref ...:
"/var/www/html/_container_data/cron/Crontab": not found
```

Vazifani boshlashdan oldin `Logging.md` faylini qayta o'qishni so'radi.

## 1-qadam: `Logging.md` faylini qayta o'qish

**Fayl:** `Logging.md`

**Nima uchun:** Foydalanuvchi har safar yangi vazifa boshlashda ushbu faylni o'qishni talab qiladi — bu safar ham log formatiga qat'iy rioya qilindi.

## 2-qadam: Xato xabarini tahlil qilish

Xato matnida ikkita muhim ma'lumot bor edi:
1. Muammo aynan `php.dockerfile`ning 21-qatorida, `COPY` buyrug'ida.
2. Xato turi — `"...": not found`, ya'ni Docker build **manba faylni build context ichida topa olmayapti** (bu runtime xatosi emas, balki build bosqichidagi, image hali yaratilmasdan turib chiqadigan xato).

**Muhim domen bilimi:** Dockerfile'dagi `COPY <src> <dest>` buyrug'ida `<src>` **har doim build context (host mashinadagi build boshlanadigan papka) ga nisbatan** hisoblanadi, image ichidagi fayl tizimiga emas — hatto `<src>` oldida `/` bo'lsa ham, Docker buni build context ildiziga nisbatan qabul qiladi va tashqi (image ichidagi) yo'lni tushunmaydi. Demak, `COPY /var/www/html/_container_data/cron/Crontab ...` qatori **build context papkasi ichida `var/www/html/_container_data/cron/Crontab` degan yo'lni** qidiradi — bunday papka esa loyihaning o'zida (host'da) umuman mavjud emas, chunki `/var/www/html` faqat **konteyner ichidagi** maqsad yo'li, host/build-context'da bunday struktura yo'q.

## 3-qadam: `docker-compose.yaml`dan build context'ni tasdiqlash

**Fayl:** `docker-compose.yaml` (ilgari, upgrade vazifasi paytida o'qilgan edi)

**Xulosa (qayta tasdiqlandi):** `php`, `artisan`, `server` xizmatlarining barchasi uchun `build.context: .` — ya'ni build context **loyihaning ildiz papkasi** (`event-organizer/`). Demak, `COPY` buyrug'idagi manba yo'llar shu ildiz papkaga nisbatan yozilishi kerak, masalan: `_container_data/cron/Crontab` (boshida `/var/www/html` bo'lmasligi kerak).

## 4-qadam: Kerakli faylning haqiqatda mavjudligini tekshirish

**Buyruq:** `test -f "_container_data/cron/Crontab" && echo "fayl mavjud" && cat "_container_data/cron/Crontab"`

**Natija:** Fayl **loyiha ildizida, to'g'ri joyda** (`_container_data/cron/Crontab`) mavjud va mazmuni:
```
* * * * /var/www/html/scripts/run-scheduler-cron.sh
```

**Xulosa:** Fayl o'zi yo'qolmagan yoki noto'g'ri joyga qo'yilmagan — muammo faqat `COPY` buyrug'idagi **noto'g'ri manba yo'li yozilishi** edi (`/var/www/html/` prefiksi ortiqcha).

## 5-qadam: Bu xato Laravel 13 upgrade bilan bog'liqmi yoki oldindan mavjud bo'lganmi — aniqlash

**Buyruq:** `git log --oneline -- _container_data/dockerfiles/php.dockerfile`

**Nima uchun:** Men (Claude) avvalgi vazifada (`task_logging_1.md`–`task_logging_4.md`) Docker fayllariga hech qanday o'zgartirish kiritmagan edim — faqat `composer.json`, `composer.lock`, `config/session.php` fayllariga tegilgan edi. Shuning uchun bu xato mening ishim natijasi emasligini **git tarixi orqali tasdiqlash** kerak edi (halllyutsinatsiya qilmaslik va noto'g'ri xulosaga kelmaslik uchun).

**Buyruq:** `git show 69f40c0 -- _container_data/dockerfiles/php.dockerfile`

**Natija:** Muammoli `COPY /var/www/html/_container_data/cron/Crontab /etc/cron.d/` qatori **`69f40c0` commit'ida ("add crontab for scheduling", 2025-09-12, muallif AlisherN) qo'shilgan** — bu mening ushbu suhbatdagi ishimdan ancha oldin, Laravel 13 upgrade vazifasidan ham oldin sodir bo'lgan. **Xulosa: bu xato Laravel framework versiyasini yangilash bilan hech qanday aloqasi yo'q — bu o'sha commit'da kiritilgan, avvaldan mavjud (pre-existing) Dockerfile xatosi.** Ehtimol, bu qator hech qachon muvaffaqiyatli build qilinmagan yoki oldin boshqacha build context bilan (masalan, to'g'ridan-to'g'ri `docker build` `_container_data/dockerfiles` papkasidan emas, balki boshqa joydan) sinovdan o'tkazilgan.

## 6-qadam: Xuddi shu commit'da yana bir yashirin xatoni aniqlash

**Fayl:** `_container_data/dockerfiles/php.dockerfile`, 3-5 qatorlar (to'liq fayl `Read` orqali qayta o'qildi)

```dockerfile
# Add crontab
RUN apk update \
    apk add cron
```

**Tahlil:** Bu yerda `\` belgisi Dockerfile'da faqat **qatorni davom ettiradi** (shell buyrug'ini bo'laklarga bo'ladi), lekin ikkita alohida buyruqni **ketma-ket bajarish uchun `&&` operatori yo'q**. Natijada bu ikki qator birlashib, bitta buyruq sifatida talqin qilinadi: `apk update apk add cron` — ya'ni `apk`ning `update` kichik buyrug'iga `apk`, `add`, `cron` degan **ortiqcha, keraksiz argumentlar** sifatida uzatiladi. Boshqacha aytganda, **`apk add cron` alohida buyruq sifatida umuman bajarilmaydi** — demak, `cron` paketi image ichida o'rnatilmay qolishi mumkin edi.

**Nima uchun bu muhim:** Fayl oxirida (80-81 qatorlar) `CMD ["cron", "-f"]` bor — ya'ni konteyner ishga tushganda `cron` dasturini foreground rejimida ishga tushirishga harakat qiladi. Agar `cron` paketi hech qachon o'rnatilmagan bo'lsa, bu buyruq **"executable file not found"** turidagi xato bilan konteynerni ishga tushira olmay qolishi mumkin edi — ya'ni birinchi xato (`COPY` yo'li) tuzatilgandan keyin ham, keyingi bosqichda **ikkinchi, boshqa xato** chiqishi mumkin edi.

**Taqqoslash uchun asos:** Xuddi shu fayldagi boshqa `RUN` bloklari (masalan, 27-43 qatorlardagi GD kengaytmasi o'rnatish bloki) to'g'ri naqshni ko'rsatadi — har bir keyingi buyruq oldida aniq `&&` operatori bor (`... \n    && docker-php-ext-configure gd ...`). Bu 4-5 qatorlardagi yozuv o'sha naqshga mos kelmasligini, ya'ni bu haqiqatan ham xato (ehtimol `&&` yozishni unutish) ekanini tasdiqlaydi.

## 7-qadam: Ikkala muammoni ham tuzatish

**Vosita:** `Edit`
**Fayl:** `_container_data/dockerfiles/php.dockerfile`

**O'zgarish 1 (3-5 qatorlar):**
```diff
- RUN apk update \
-     apk add cron
+ RUN apk update \
+     && apk add cron
```
**Sabab:** `apk update` va `apk add cron` endi ikkita alohida, ketma-ket bajariladigan buyruq sifatida to'g'ri bog'landi — bu fayldagi boshqa `RUN` bloklarida ishlatilgan naqsh bilan bir xil.

**O'zgarish 2 (21-qator):**
```diff
- COPY /var/www/html/_container_data/cron/Crontab /etc/cron.d/
+ COPY _container_data/cron/Crontab /etc/cron.d/
```
**Sabab:** Manba yo'l endi build context (loyiha ildizi)ga nisbatan to'g'ri yozildi — `/var/www/html` prefiksi olib tashlandi, chunki bu faqat maqsad (destination, konteyner ichidagi) yo'lida kerak, manba yo'lida emas.

## 8-qadam: Tuzatishni lokal Docker orqali tasdiqlash

**Buyruq:** `docker --version`, `docker info` — Docker Desktop lokal muhitda mavjud va ishlayotgani tasdiqlandi (versiya 29.6.2).

**Buyruq:** `docker compose build php`

**Birinchi natija — YANGI xato chiqdi:**
```
#8 [ 2/13] RUN apk update     && apk add cron
ERROR: unable to select packages:
  cron (no such package): required by: world[cron]
```

**Tahlil:** Bu **3-tuzatish (`&&` qo'shish)ning to'g'ri ishlaganini bevosita isbotladi** — endi `apk add cron` haqiqatan ham alohida buyruq sifatida bajarilmoqda (avval umuman bajarilmagan edi). Lekin bu buyruqning o'zi **muvaffaqiyatsiz**, chunki Alpine Linux repozitoriyasida **`cron` degan nomdagi paket umuman mavjud emas**.

**Qo'shimcha tekshiruv:** `docker run --rm php:8.4-fpm-alpine sh -c "apk update; apk search cron"` — loyihada ishlatilayotgan xuddi shu baza image (`php:8.4-fpm-alpine`) ichida qidiruv qildim. Natijada `cron` nomli paket yo'q, lekin muqobillari bor: `dcron`, `cronie`, `fcron`, `apk-cron` (bu oxirgisi shunchaki "apk paketlarini davriy yangilash" uchun, cron daemoni emas — chalg'ituvchi nom).

**Qo'shimcha tekshiruv:** `docker run --rm php:8.4-fpm-alpine sh -c "which crond; busybox | grep cron"` — bazaviy image'ning o'zida **BusyBox orqali `/usr/sbin/crond` allaqachon mavjud** ekanini aniqladim (qo'shimcha paket o'rnatmasdan ham cron demoni ishlaydi). Ammo Dockerfile oxiridagi `CMD ["cron", "-f"]` baribir **"cron" nomli ijro etiluvchi faylni** chaqiradi, u esa hech qanday paket bilan (na busybox, na dcron, na cronie) ta'minlanmaydi — hammasi faqat `crond` nomli demon beradi, `cron` emas.

**Xulosa:** Bu **to'rtinchi mustaqil xato** (avvalgi ikkitasidan farqli, lekin ular bilan bir commit'da, `69f40c0`da kiritilgan): (a) `cron` nomli paket mavjud emas va (b) `CMD` da chaqirilayotgan `cron` binary fayli hech qanday sharoitda mavjud bo'lmaydi (to'g'ri nomi — `crond`).

## 9-qadam: To'g'ri paket nomini va CMD'ni tanlash uchun sinov

**Buyruq:**
```
docker run --rm php:8.4-fpm-alpine sh -c "apk update; apk add dcron; crond -h"
```
**Natija:** `dcron` paketi muvaffaqiyatli o'rnatildi va `cmd:crond`, `cmd:crontab` ni ta'minlaydi. `crond -h` yordam matnida muhim tafsilot topildi:
```
-s  directory of system crontabs (defaults to /etc/cron.d)
-c  directory of per-user crontabs (defaults to /etc/crontabs)
```
**Xulosa:** `dcron`ning `crond` dasturi **standart holatda aynan `/etc/cron.d`** papkasini "system crontabs" manbai sifatida o'qiydi — bu loyihadagi `COPY ... /etc/cron.d/` qatori bilan **to'g'ridan-to'g'ri mos keladi**! Ya'ni hech qanday qo'shimcha `-c`/`-s` flag kerak emas, standart `crond -f` buyrug'ining o'zi `/etc/cron.d/Crontab` faylini avtomatik topadi.

**Amaliy tasdiqlash:** Konteyner ichida `/etc/cron.d/Crontab`ga to'g'ri formatdagi (`* * * * * echo hi`, 5 ta vaqt maydoni) test yozuvini qo'yib, `crond -f -d 8 -c /etc/cron.d` orqali ishga tushirdim — natijada `crond` uni muvaffaqiyatli o'qidi va "User root Entry * * * * * echo hi" deb tan oldi, xatosiz.

## 10-qadam: Dockerfile'ga yakuniy tuzatishlarni kiritish

**Vosita:** `Edit`
**Fayl:** `_container_data/dockerfiles/php.dockerfile`

**O'zgarish 3 (4-5 qatorlar):**
```diff
- RUN apk update \
-     && apk add cron
+ RUN apk update \
+     && apk add dcron
```
**Sabab:** `cron` mavjud bo'lmagan paket nomi edi; `dcron` — Alpine repozitoriyasida haqiqatan mavjud, `crond`/`crontab` buyruqlarini ta'minlovchi yengil cron demoni paketi.

**O'zgarish 4 (oxirgi qator, avval 81):**
```diff
- CMD ["cron", "-f"]
+ CMD ["crond", "-f", "-L", "/var/log/cron.log"]
```
**Sabab:**
- `cron` — hech qachon mavjud bo'lmagan ijro fayli, `crond`ga almashtirildi (dcron paketi shuni ta'minlaydi).
- `-L /var/log/cron.log` — bu flag ataylab qo'shildi, chunki Dockerfile'ning bir qatori tepada (`RUN ln -sf /dev/stdout /var/log/cron.log`, muallif tomonidan avvaldan yozilgan) aynan shu faylni `stdout`ga bog'laydi — ya'ni asl muallifning niyati crond loglari shu fayl orqali `docker logs` orqali ko'rinishi edi. Lekin `crond` standart holatda syslog'ga yozadi (`-S`, standart), `/var/log/cron.log`ga emas — shuning uchun bu symlink qatori **hech qachon ishlatilmay, "o'lik kod" bo'lib qolar edi**. `-L` flagini qo'shish orqali muallifning asl niyatini haqiqiy ishlaydigan holga keltirdim.
- Alohida `-c`/`-s` flag qo'shilmadi, chunki 9-qadamda tasdiqlanganidek, `dcron`ning standart sozlamasi (`/etc/cron.d`) loyihadagi COPY manzili bilan aynan mos keladi.

## 11-qadam: Tuzatilgan Dockerfile bilan qayta build qilish — muvaffaqiyat

**Buyruq:** `docker compose build php`

**Natija:** ✅ **Build to'liq muvaffaqiyatli yakunlandi** ("Image event-organizer-php Built"), barcha 13 bosqich (jumladan avval xato bergan cron o'rnatish va Crontab COPY bosqichlari) muammosiz o'tdi.

**Buyruq:** `docker compose build server artisan`

**Natija:** ✅ Ikkala xizmat ham (`event-organizer-server`, `event-organizer-artisan`) muvaffaqiyatli build qilindi — ular ham xuddi shu `php.dockerfile`dan (yoki undan bog'liq bosqichlardan) foydalanadi, shuning uchun ularni ham alohida tekshirish muhim edi.

## 12-qadam: Yakuniy amaliy tekshiruv — `crond` haqiqiy build qilingan image ichida ishga tushiriladimi?

**Buyruq:**
```
docker run --rm event-organizer-php sh -c "timeout 2 crond -f -L /var/log/cron.log; ls -la /etc/cron.d/"
```

**Natija:**
```
crond 4.5 dillon's cron daemon, started with loglevel notice
failed parsing crontab for user root: /var/www/html/scripts/run-scheduler-cron.sh
Terminated
```
va `/etc/cron.d/Crontab` fayli konteyner ichida to'g'ri joyda, to'g'ri huquqlar bilan (`-rwxr-xr-x`) mavjudligi tasdiqlandi.

**MUHIM YANGI TOPILMA (Docker build xatosiga aloqasi yo'q, lekin jiddiy funksional xato):** `crond` demonining o'zi muvaffaqiyatli ishga tushdi (build va CMD tuzatishlari to'g'ri ekanini yana bir bor tasdiqladi), LEKIN u `/etc/cron.d/Crontab` faylini **parslay olmadi**: `failed parsing crontab for user root: /var/www/html/scripts/run-scheduler-cron.sh`. Sababi — `_container_data/cron/Crontab` faylining mazmuni:
```
* * * * /var/www/html/scripts/run-scheduler-cron.sh
```
Standart cron formatida **5 ta vaqt maydoni** kerak (minut, soat, kun, oy, hafta kuni) + buyruq, lekin bu faylda atigi **4 ta yulduzcha** bor (hafta-kuni maydoni tushib qolgan) — natijada `crond` skript yo'lini noto'g'ri vaqt maydoni deb talqin qilishga urinadi va xato beradi. **Bu Docker build xatosi emas** (build muvaffaqiyatli o'tadi, chunki Docker build vaqtida crontab sintaksisini tekshirmaydi) — bu **runtime (ishga tushirilgandan keyingi) xato**, ya'ni konteyner ishga tushadi, lekin rejalashtirilgan vazifa (`events:process-status` buyrug'ini ishga tushirish) hech qachon amalga oshmaydi.

**Qo'shimcha kuzatuv:** `scripts/run-scheduler-cron.sh` fayli avvalroq (`task_logging`dagi upgrade vazifasida emas, balki ushbu vazifada) `Read` orqali tekshirilganda **butunlay bo'sh (0 bayt)** ekani ma'lum bo'ldi. Ya'ni hatto crontab formati to'g'irlansa ham, bu skript hozircha **hech narsa bajarmaydi** — demak, `events`/`event` nom nomuvofiqligi (`task_logging_4.md`da qayd etilgan) va bu bo'sh skript birgalikda "tadbir holatini avtomatik yangilash" funksiyasini butunlay ishlamay qo'yishi mumkin.

## Yakuniy xulosa

**Docker build xatosi (foydalanuvchi xabar bergan asosiy muammo) to'liq hal qilindi va amalda tasdiqlandi** (`docker compose build php/server/artisan` — barchasi muvaffaqiyatli). Buning uchun `_container_data/dockerfiles/php.dockerfile`da **to'rtta** o'zaro bog'liq, lekin mustaqil xato tuzatildi (barchasi `69f40c0` commit'ida kiritilgan, Laravel 13 upgrade'iga aloqasi yo'q):
1. `apk update` va `apk add cron` orasida yo'q qilingan `&&` operatori.
2. `COPY /var/www/html/_container_data/cron/Crontab ...` — build context'ga nisbatan noto'g'ri (mavjud bo'lmagan) manba yo'li.
3. `cron` — Alpine'da mavjud bo'lmagan paket nomi (`dcron`ga almashtirildi).
4. `CMD ["cron", "-f"]` — mavjud bo'lmagan ijro fayli nomi (`crond`ga almashtirildi, log fayliga yozish uchun `-L` flagi ham qo'shildi).

Bundan tashqari, tuzatishlarni **amalda sinab ko'rish jarayonida** (shunchaki "build o'tdi" deb to'xtamasdan, `crond`ni haqiqatan ishga tushirib) **beshinchi, alohida runtime xatosi** kashf qilindi: `_container_data/cron/Crontab` faylida cron vaqt maydonlaridan biri (hafta kuni) tushib qolgan, shuning uchun `crond` uni parslay olmayapti. Bundan tashqari, chaqirilayotgan `scripts/run-scheduler-cron.sh` skripti bo'sh. **Bu ikkala narsa ham Docker build muvaffaqiyatiga ta'sir qilmaydi** (shuning uchun ular ushbu vazifa doirasida — foydalanuvchining aniq so'ragan "build xatosini top va hal qil" so'rovi doirasida — **tuzatilmadi**), lekin ular tuzatilmasa, konteyner build va ishga tushishi muvaffaqiyatli bo'lsa-da, **rejalashtirilgan vazifa (scheduler) baribir hech qachon ishlamaydi**. Bu haqda foydalanuvchiga alohida xabar berish va tasdiqlashini so'rash kerak.
