# Task Logging 3 — Kod darajasidagi o'zgarishlar va amaliy tekshiruv (testlar)

## 1-qadam: CSRF middleware nomi o'zgarishini loyiha kodida qidirish

**Vosita:** `Grep`
**Naqsh:** `VerifyCsrfToken|ValidateCsrfToken|CsrfToken|Sec-Fetch`
**Qidirilgan joy:** butun loyiha (vendor'dan tashqari)

**Nima uchun:** `task_logging_1.md`da aniqlangan "Yuqori ta'sir" o'zgarish — Laravel 13'da CSRF middleware `VerifyCsrfToken`dan `PreventRequestForgery`ga o'zgartirilgan. Loyiha kodida bu klasslarga to'g'ridan-to'g'ri murojaat (masalan, `bootstrap/app.php`dagi `withoutMiddleware()` yoki testlardagi `withoutMiddleware()`) bor-yo'qligini tekshirish kerak edi.

**Natija:** Faqat `task_logging/task_logging_1.md`ning o'zida (mening avvalgi yozgan tahlil matnimda) moslik topildi — bu haqiqiy kod emas. **Loyiha kodida bu klasslarga bevosita murojaat yo'q.** Xulosa: hech qanday kod o'zgartirish shart emas, chunki middleware nomini Laravel o'zi ichki ravishda avtomatik boshqaradi (eski nom deprecated alias sifatida ham ishlayveradi).

## 2-qadam: Test/artisan buyruqlarini ishga tushirish uchun `.env` faylini yaratish

**Buyruq:** `cp .env.example .env`

**Nima uchun:** Loyihada avval `.env` fayli umuman yo'q edi (faqat `.env.example`). Composer/artisan buyruqlari (`key:generate`, `test`, `route:list`, `about`) ishlashi uchun `.env` fayli zarur. `.gitignore`ni tekshirdim — `.env` u yerda ro'yxatga olingan, ya'ni bu fayl versiyalashga tushmaydi va faqat lokal ishga tushirish uchun xizmat qiladi.

**Buyruq:** `php artisan key:generate --ansi`

**Nima uchun:** `.env.example`da `APP_KEY` bo'sh, Laravel APP_KEY'siz ishlay olmaydi (session shifrlash, encryption kabi funksiyalar uchun kerak). Buyruq muvaffaqiyatli yakunlandi: "Application key set successfully."

## 3-qadam: Test ishga tushirish uchun kerakli PHP kengaytmasini (pdo_sqlite) tekshirish va yoqish

**Fayl:** `phpunit.xml` (avval o'qilgan, `task_logging_1.md`da) — testlar uchun `DB_CONNECTION=sqlite`, `DB_DATABASE=:memory:` belgilangan.

**Buyruq:** `php -m | grep -i sqlite` — bo'sh natija, ya'ni `pdo_sqlite` kengaytmasi yuklanmagan edi.

**Tekshiruv:** `grep -in "sqlite" "C:\php84\php.ini"` va `ls "C:\php84\ext" | grep -i sqlite` — natija: `php_pdo_sqlite.dll` va `php_sqlite3.dll` fayllari DLL sifatida mavjud, faqat `php.ini`da izohga olingan (`;extension=pdo_sqlite`).

**Fayl tahrirlandi:** `C:\php84\php.ini` (937-qator)
**O'zgarish:** `;extension=pdo_sqlite` → `extension=pdo_sqlite`

**Nima uchun bu qadam zarur va xavfsiz edi:** `task_logging_2.md`dagi GD kengaytmasi holatiga o'xshab, bu ham faqat lokal PHP CLI muhitida o'chirilgan, allaqachon mavjud kengaytmani yoqish — hech narsa yangi o'rnatilmadi. Bu sof test infratuzilmasi ehtiyoji (PHPUnit'ning `:memory:` SQLite bazasi orqali testlarni tezkor ishga tushirishi uchun), Laravel 13'ning o'zi bilan bog'liq talab emas — lekin uni hal qilmasdan test to'plamini umuman ishga tushirib bo'lmas edi.

**Tekshiruv:** `php -m | grep -i sqlite` qaytadan ishga tushirildi — natija: `pdo_sqlite` — muvaffaqiyatli yoqilgani tasdiqlandi.

## 4-qadam: `config/session.php`ga `serialization` sozlamasini qo'shish

**Vosita:** `Edit`
**Fayl:** `config/session.php`

**Qo'shilgan qator (yangi bo'lim, `'encrypt'` kalitidan keyin):**
```php
'serialization' => env('SESSION_SERIALIZATION', 'php'),
```

**Nima uchun:** `task_logging_1.md`da aniqlangan "Session serialization Configuration" o'zgarishiga javoban (rasmiy upgrade guide, "Low Impact" toifasi). Laravel 13'ning yangi skeleton fayli bu qiymatni `'json'` qilib beradi (deserializatsiya hujumlaridan himoya uchun), lekin agar mavjud ishlab turgan ilovada bu qiymat o'zgartirilsa — **barcha faol foydalanuvchi sessiyalari (login holatlari) bekor bo'ladi**, chunki eski `php`-formatdagi sessiya ma'lumotlari yangi `json` formatda o'qib bo'lmaydi. Loyihaning joriy `config/session.php` faylida bu kalit umuman yo'q edi, shuning uchun men rasmiy guide'ning tavsiyasiga amal qilib, **eski xatti-harakatni saqlab qolish** uchun uni aniq (explicit) ravishda `'php'` qilib qo'ydim — bu upgrade paytida foydalanuvchilarni tasodifan tizimdan chiqarib yubormaslik uchun. Agar kelajakda xavfsizlikni kuchaytirish maqsadida `json`ga o'tish kerak bo'lsa, buni ongli ravishda, foydalanuvchilarni oldindan ogohlantirib, alohida qaror sifatida qilish tavsiya etiladi.

## 5-qadam: Frontend bog'liqliklarini o'rnatish va build qilish

**Buyruqlar:**
- `npm install` — 180 ta paket o'rnatildi. (Node.js v25.4.0, npm 11.7.0 versiyalari `node --version`/`npm --version` orqali oldindan tekshirildi.)
- `npm run build` — Vite orqali production build muvaffaqiyatli yakunlandi, `public/build/manifest.json` va boshqa asset fayllar (CSS/JS) yaratildi.

**Nima uchun kerak edi:** `resources/views/layouts/main.blade.php` fayli `@vite(...)` direktivasi orqali `public/build/manifest.json` faylini talab qiladi. Bu fayl mavjud bo'lmasa, har qanday sahifани render qilishga urinish `ViteManifestNotFoundException` xatosini beradi (buni keyingi qadamda test orqali ham ko'rdim). Bu Laravel 13 upgrade bilan bog'liq emas — loyihada shunchaki frontend build hali ishga tushirilmagan edi.

**Yon ta'sir:** `npm install` natijasida `package-lock.json` faylida bitta qator o'zgardi: loyiha nomi `"html"` dan `"event-organizer"`ga to'g'irlandi (`package.json`da `"name"` maydoni yo'qligi sababli npm papka nomidan avtomatik xulosa chiqaradi; ilgari boshqa nomdagi papkada `npm install` qilingan bo'lishi mumkin). Bu zararsiz, tasodifiy to'g'rilanish, Laravel upgrade vazifasiga aloqasi yo'q, shuning uchun ortga qaytarilmadi.

## 6-qadam: To'liq test to'plamini ishga tushirish (birinchi urinish)

**Buyruqlar:** `php artisan config:clear --ansi && php artisan test`

**Natija:** 2 testdan 1 tasi (`Tests\Unit\ExampleTest`) o'tdi, 1 tasi (`Tests\Feature\ExampleTest::test_the_application_returns_a_successful_response`) muvaffaqiyatsiz tugadi.

**Xatolikni tahlil qilish:** Stack trace `Illuminate\Foundation\ViteManifestNotFoundException`ni ko'rsatdi — `public/build/manifest.json` topilmadi. **Muhim kuzatuv:** stack trace ichida `Illuminate\Foundation\Http\Middleware\PreventRequestForgery` klassi **allaqachon avtomatik ravishda** so'rov pipeline'ida ishlatilayotganini ko'rdim (13-qatorda) — bu Laravel 13'ning yangi CSRF middleware nomi hech qanday qo'shimcha kod o'zgarishisiz, avtomatik ravishda ishga tushayotganini tasdiqladi (2-qadamdagi xulosamni amaliy testda tasdiqladi).

**Xulosa:** Bu xatolik Laravel 13 upgrade'ining nosozligi emas, balki shunchaki 5-qadamda hali bajarilmagan frontend build muammosi edi (build 5-qadamda alohida, testlardan oldin bajarilgan bo'lsa ham, bu test ishga tushirilgan payt build fayllari hali yaratilmagan edi — chunki men buni ketma-ket emas, deyarli bir vaqtda amalga oshirgandim; keyingi urinishda tasdiqlandi).

## 7-qadam: Test to'plamini qayta ishga tushirish (build tugagandan keyin)

**Buyruq:** `php artisan test`

**Natija:** ✅ **2 testdan 2 tasi o'tdi** (`Tests: 2 passed (2 assertions)`, `Duration: 0.53s`).

**Xulosa:** `public/build/manifest.json` yaratilgandan so'ng barcha mavjud testlar muvaffaqiyatli o'tdi. Bu loyihaning asosiy HTTP so'rov-javob sikli (routing → middleware, jumladan yangi `PreventRequestForgery` → view rendering) Laravel 13'da to'g'ri ishlayotganini tasdiqlaydi.

**Eslatma:** Loyihada faqat 2 ta "misol" (`Example`) test mavjud (`tests/Unit/ExampleTest.php`, `tests/Feature/ExampleTest.php`) — bular ilovaning maxsus biznes-logikasini (masalan, `JoinEventCommandHandler`, `EventRepository` kabi) sinovdan o'tkazmaydi. Shuning uchun bu testlarning o'tishi faqat **framework darajasidagi** (bootstrapping, routing, middleware stack, view rendering) moslikni tasdiqlaydi, ilovaning barcha domenga xos ish mantig'ini emas.

## 8-qadam: Marshrutlar (routes) ro'yxatini tekshirish

**Buyruq:** `php artisan route:list --ansi`

**Natija:** Jami **38 ta marshrut** hech qanday xatoliksiz ro'yxatlandi — jumladan `events.*`, `profile.*`, `auth` marshrutlari, `mews/captcha` paketining `captcha/{config?}` va `captcha/api/{config?}` marshrutlari, va `laravel/sanctum` paketi tomonidan avtomatik ro'yxatga olinadigan `sanctum/csrf-cookie` marshruti.

**Xulosa:** Bu shuni ko'rsatadiki — barcha service provider'lar (jumladan `AppServiceProvider`, `RepositoryServiceProvider`, `mews/captcha` va `laravel/sanctum` paketlarining o'z provider'lari) Laravel 13 ostida muammosiz yuklanmoqda, class/metod nomlari mos kelmasligi kabi runtime xatoliklar yo'q.

## 9-qadam: Umumiy ilova holatini tekshirish

**Buyruq:** `php artisan about --ansi`

**Natija (muhim qatorlar):**
- `Laravel Version`: **13.25.0** — versiya muvaffaqiyatli 12.x'dan 13.x'ga ko'tarilgani tasdiqlandi.
- `PHP Version`: 8.4.1 — Laravel 13'ning `^8.2` talabiga mos.
- Boshqa barcha drayverlar (Cache: database, Session: database, Queue: database, Mail: smtp) o'zgarishsiz, kutilganidek ko'rsatildi.

**Eslatma:** `Storage: public/storage NOT LINKED` deb ko'rsatildi — bu upgrade bilan bog'liq emas, loyihada `php artisan storage:link` hali bajarilmagan (mavjud, oldindan kelgan holat), shuning uchun bu vazifa doirasida tuzatilmadi.

## 10-qadam: Xatolik jurnalini (log) tekshirish

**Buyruq:** `test -f storage/logs/laravel.log && tail -100 storage/logs/laravel.log`

**Natija:** Faylda faqat 6-qadamdagi (build tugallanishidan oldingi, keyin 7-qadamda muvaffaqiyatli tuzatilgan) `ViteManifestNotFoundException`ning eski yozuvi qoldi. **Yangi/qo'shimcha xato yoki "deprecated" ogohlantirish topilmadi.**

## 11-qadam: Git holatini yakuniy tekshirish

**Buyruqlar:** `git status --short`, `git diff --stat package-lock.json`, `git diff package-lock.json`

**Natija:** O'zgargan fayllar:
- `composer.json` — versiya cheklovlari yangilandi (`task_logging_2.md`, 1 va 5-qadamlar)
- `composer.lock` — yangi bog'liqlik grafigi bilan qayta yozildi (`composer update` natijasi)
- `config/session.php` — `serialization` kaliti qo'shildi (ushbu faylning 4-qadami)
- `package-lock.json` — loyiha nomi (`name` maydoni) to'g'irlandi (5-qadamdagi yon ta'sir, zararsiz)
- `task_logging/` — yangi papka (bu hujjatlashtirish fayllari)

`.env` fayli **ro'yxatda yo'q** — bu to'g'ri, chunki `.gitignore` uni istisno qiladi (2-qadobda tekshirilgan).

## Xulosa

Laravel frameworkini **12.x dan 13.25.0 versiyaga** muvaffaqiyatli upgrade qildim. Amalga oshirilgan barcha o'zgarishlar:

1. `composer.json`: `laravel/framework` → `^13.0`, `laravel/tinker` → `^3.0`, `phpunit/phpunit` (dev) → `^12.0`, `mews/captcha` → `^3.5` (versiyasiz `*`dan aniqlashtirildi).
2. `config/session.php`: yangi `'serialization' => env('SESSION_SERIALIZATION', 'php')` kaliti qo'shildi — mavjud foydalanuvchi sessiyalarini upgrade paytida bekor qilib yubormaslik uchun.
3. Loyiha kodida (`app/`, `routes/`, va h.k.) Laravel 13'ning "Yuqori"/"O'rta" ta'sir toifasidagi boshqa breaking change'lariga (CSRF middleware nomi, `upsert` uchun `uniqueBy`, polymorphic pivot table nomlari, queue event property nomlari, global helper funksiya konfliktlari) tegishli **hech qanday moslik-buzilishi topilmadi** — bular avtomatik ravishda framework darajasida hal bo'ladi yoki loyihada umuman ishlatilmagan.
4. Amaliy tekshiruv: to'liq test to'plami (2/2), `route:list` (38 marshrut, xatosiz), `about` buyrug'i orqali versiya tasdiqlandi, log faylida yangi xato yo'qligi tekshirildi.

**Loyihadan tashqari (lokal muhit) qilingan qo'shimcha ishlar** (upgrade jarayonini sinash uchun zarur bo'lgan, lekin repozitoriyga tegishli bo'lmagan): lokal `C:\php84\php.ini` faylida `gd` va `pdo_sqlite` kengaytmalari yoqildi (ikkalasi ham DLL sifatida allaqachon mavjud edi, faqat o'chirilgan holatda edi) — bular production Docker muhitida (`php.dockerfile`) allaqachon yoqilgan/kerak bo'lgan kengaytmalar bilan bir xil, shuning uchun lokal muhitni production bilan muvofiqlashtirish sifatida qaraladi.
