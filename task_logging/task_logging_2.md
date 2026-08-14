# Task Logging 2 — `composer.json`ni yangilash va bog'liqliklarni o'rnatish

## 1-qadam: `composer.json` faylida versiya cheklovlarini o'zgartirish

**Vosita:** `Edit`
**Fayl:** `composer.json`

**O'zgarishlar:**
- `"laravel/framework": "^12.0"` → `"laravel/framework": "^13.0"`
- `"laravel/tinker": "^2.10.1"` → `"laravel/tinker": "^3.0"`
- `"phpunit/phpunit": "^11.5.3"` (require-dev) → `"phpunit/phpunit": "^12.0"`

**Nima uchun:** Bular rasmiy Laravel 13 Upgrade Guide'da (`task_logging_1.md`, 6-qadamga qarang) "Yuqori ta'sir" toifasida aniq ko'rsatilgan majburiy versiya yangilanishlari.

## 2-qadam: Birinchi `composer update` urinishi — muvaffaqiyatsiz

**Buyruq:** `composer update --no-interaction --no-scripts`

**Nima uchun `--no-scripts`:** `post-update-cmd` skripti (`artisan vendor:publish --tag=laravel-assets`) va boshqa post-install skriptlar ishga tushishi uchun to'liq ishlaydigan `.env`/`APP_KEY` talab qilinishi mumkin edi; bog'liqliklar hali resolve bo'lmagan bosqichda bunday skriptlarni ishga tushirish keraksiz xatolarga olib kelishi mumkin edi, shuning uchun avval faqat paketlarni yechishga (dependency resolution) e'tibor qaratildi.

**Natija — xatolik:** Composer bog'liqliklarni yecha olmadi. Composer chiqargan xabarni tahlil qildim:

- `mews/captcha` composer.json'da versiyasiz (`"*"`) belgilangan edi. Bu Composer solver'iga istalgan versiyani (hatto 2016-yillardagi `1.0.1` kabi juda eski versiyalarni ham) ko'rib chiqishga imkon berdi.
- Muammoning ildizi: `mews/captcha`ning zamonaviy versiyalari (`2.0.0` dan yuqorisi va `3.5.0` gacha) ishlashi uchun **`ext-gd`** (PHP GD kengaytmasi) talab qiladi, lekin mening lokal PHP muhitimda bu kengaytma o'chirilgan edi. Natijada Composer'ning solver algoritmi GD talab qilmaydigan versiyalarni qidirib, `illuminate/support ~4` yoki `~5` talab qiladigan juda eski `mews/captcha 1.0.1`/`1.0.2` versiyalariga qadar pastga tushib ketdi — bular esa `laravel/framework ^13.0` bilan mutlaqo mos kelmaydi (chunki `laravel/framework` paketi `illuminate/support`ni o'zi bilan birga "almashtiradi" (`replaces`) va ikkalasi bir vaqtda mavjud bo'la olmaydi).
- Xulosa: bu **Laravel 13'ning o'zi bilan bog'liq muammo emas**, balki (1) composer.json'da `mews/captcha` uchun versiya cheklovi umuman yo'qligi va (2) lokal PHP muhitida `gd` kengaytmasi o'chirilganligi sabab bo'lgan yon ta'sir edi.

## 3-qadam: Muammoni tashxislash — GD kengaytmasi holatini tekshirish

**Buyruqlar:**
- `php -m | grep -i gd` — bo'sh natija qaytardi, ya'ni `gd` kengaytmasi yuklanmagan.
- `php --ini` — natija: `Loaded Configuration File: C:\php84\php.ini`. Bu fayl qayerdaligini aniqlash uchun ishlatildi.
- `composer show mews/captcha -a` — paket tavsifi va mavjud versiyalar ro'yxatini ko'rish uchun (natija: eng so'nggi versiya `3.5.0`).

**Fayl o'qildi:** `_container_data/dockerfiles/php.dockerfile`

**Nima uchun o'qildi:** Loyihaning **haqiqiy production/dev muhiti** (Docker orqali) `gd` kengaytmasini talab qiladimi yo'qmi, buni tekshirish uchun. Agar production muhitda ham `gd` yo'q bo'lsa, bu jiddiy infratuzilma muammosi bo'lardi.

**Xulosa:** Dockerfile'ning 26–43 qatorlarida aniq ko'rsatilgan: `freetype`, `libjpeg-turbo`, `libpng` kutubxonalari o'rnatilib, keyin `docker-php-ext-configure gd` va `docker-php-ext-install gd` orqali GD kengaytmasi **ataylab o'rnatilgan va yoqilgan**. Demak, production/Docker muhitida `mews/captcha`ning zamonaviy versiyasi (GD talab qiluvchi) muammosiz ishlaydi — muammo faqat mening **lokal Windows PHP CLI** muhitida edi.

**Qo'shimcha tekshiruv:** `git log --oneline -- composer.lock` va `git show HEAD:composer.lock | grep -A5 '"name": "mews/captcha"'` orqali loyihada oldin haqiqatda ishlatilgan/lock qilingan versiyani tekshirdim — natija: `mews/captcha` versiyasi `3.4.6` bo'lib, bu ham GD talab qiladigan versiya. Bu production muhitda GD borligini yana bir bor tasdiqladi.

## 4-qadam: Lokal PHP muhitida GD kengaytmasini yoqish

**Buyruq:** `ls "C:\php84\ext" | grep -i gd` — natija: `php_gd.dll` fayli **mavjud** (ya'ni kengaytma DLL'i o'rnatilgan, faqat `php.ini`da o'chirilgan/izohga olingan).

**Fayl o'qildi va tahrirlandi:** `C:\php84\php.ini` (loyiha tashqarisidagi global lokal PHP konfiguratsiyasi)

**Qilingan o'zgarish:** 923-qatordagi `;extension=gd` qatoridan boshidagi `;` (izoh belgisi) olib tashlandi, natijada `extension=gd` bo'ldi.

**Nima uchun bu xavfsiz deb topildi:**
1. DLL fayli allaqachon PHP o'rnatmasida mavjud edi — yangi narsa o'rnatilmadi, faqat mavjud, ammo o'chirilgan kengaytma yoqildi.
2. Bu aynan production Docker muhitida (`php.dockerfile`) qasddan yoqilgan xuddi shu kengaytma — ya'ni lokal muhitni production bilan **muvofiqlashtirish**, undan chetga chiqish emas.
3. `mews/captcha` paketi o'z tabiatiga ko'ra CAPTCHA rasmini generatsiya qilish uchun GD (yoki Imagick)ga muhtoj — bu loyihaning captcha route'i (`routes/web.php`dagi `/captcha/{config?}`) to'g'ri ishlashi uchun umuman zarur kengaytma, upgrade bilan bog'liq qo'shimcha talab emas.

**Tekshiruv:** `php -m | grep -i gd` qaytadan ishga tushirildi — natija: `gd` — kengaytma muvaffaqiyatli yoqilgani tasdiqlandi.

## 5-qadam: `mews/captcha` uchun versiya cheklovini aniqlashtirish

**Vosita:** `Edit`
**Fayl:** `composer.json`
**O'zgarish:** `"mews/captcha": "*"` → `"mews/captcha": "^3.5"`

**Nima uchun:** Versiyasiz (`"*"`) cheklov Composer solver'ini nazoratsiz qoldiradi va kelajakda yana shunga o'xshash keraksiz eski-versiya konfliktlariga olib kelishi mumkin. `^3.5` — bu paketning eng so'nggi barqaror (stable) versiyasi bo'lib, tavsifida "Laravel 5/6/7/8/9/10/11/12 Captcha Package" deb yozilgan (13 hali rasmiy tavsifda ko'rsatilmagan, lekin quyidagi qadamda Composer solver orqali haqiqiy moslik tasdiqlandi).

## 6-qadam: Ikkinchi `composer update` urinishi — muvaffaqiyatli bog'liqlik yechimi

**Buyruq:** `composer update --no-interaction --no-scripts`

**Natija:** Composer barcha bog'liqliklarni muvaffaqiyatli yechdi (114 ta paket o'rnatildi/yangilandi) va `composer.lock` faylini yangiladi. Asosiy paketlar quyidagi versiyalarga ko'tarildi:
- `laravel/framework` → **v13.25.0**
- `laravel/sanctum` → **v4.3.3**
- `laravel/tinker` → **v3.0.2**
- `laravel/ui` → **v4.6.3**
- `mews/captcha` → **3.5.0**
- `phpunit/phpunit` → **12.5.33**

Jarayon oxirida bitta xatolik chiqdi:
```
file_put_contents(.../vendor/composer/installed.php): Failed to open stream: Resource temporarily unavailable
```

**Tahlil:** Bu xatolik paketlarning 114 tasi ham muvaffaqiyatli yuklab olinib, extract qilingandan **keyin**, faqat oxirgi metadata faylini (`installed.php`) yozishda yuz berdi. Xato matni ("Resource temporarily unavailable") diskka yozishda vaqtinchalik operatsion tizim darajasidagi bandlik (masalan, antivirus yoki fayl indekslash xizmati faylni vaqtincha bloklashi) belgisi, haqiqiy bog'liqlik konflikti emas.

## 7-qadam: Tugallanmagan o'rnatishni qayta ishga tushirish

**Buyruq:** `composer install --no-interaction --no-scripts`

**Nima uchun:** `composer.lock` fayli allaqachon to'g'ri yozilgan edi (6-qadamda "Writing lock file" muvaffaqiyatli bo'lgan), shuning uchun `composer install`ni qayta ishga tushirish avvalgi vaqtinchalik xatoni tuzatib, faqat qolgan metadata/autoload fayllarini yozib chiqishi kerak edi.

**Natija:** `Nothing to install, update or remove` + `Generating optimized autoload files` — muvaffaqiyatli yakunlandi. Bu tasdiqladi: 6-qadamdagi xato haqiqatan ham vaqtinchalik (transient) fayl tizimi muammosi bo'lgan, bog'liqliklar yechimi(resolution)ning o'ziga aloqasi yo'q edi.

## 8-qadam: O'rnatilgan versiyalarni tasdiqlash

**Buyruq:** `composer show -i --no-ansi | grep -E "laravel/framework|laravel/sanctum|laravel/tinker|laravel/ui|phpunit/phpunit|mews/captcha"`

**Natija:**
```
laravel/framework   13.25.0
laravel/sanctum     4.3.3
laravel/tinker      3.0.2
laravel/ui          4.6.3
mews/captcha        3.5.0
phpunit/phpunit     12.5.33
```

**Xulosa:** Barcha asosiy bog'liqliklar muvaffaqiyatli Laravel 13-mos versiyalarga ko'tarildi. `vendor/` papkasi endi to'liq o'rnatilgan.

## Keyingi qadam

`task_logging_3.md` faylida:
1. `config/session.php`ga `serialization` kalitini qo'shish (mavjud sessiyalarni saqlab qolish uchun `'php'` qiymatida, `task_logging_1.md`dagi 6-qadam, "Session serialization" bandiga asosan).
2. `bootstrap/app.php` va boshqa joylarda CSRF middleware'ga oid kod borligini qayta tekshirish (yangi `PreventRequestForgery` nomi bilan mosligini ta'minlash).
3. `composer test` / `php artisan test` orqali test to'plamini ishga tushirish va natijalarni tahlil qilish.
4. Zarur bo'lsa, testlardan chiqqan xatolarni tuzatish.
