### Claude vazifalarini loglash

Claude bajarayotgan har bir amalni loglab borish kerak. Buning uchun claude o'zi bajargan har bir vazifadagi har bir amalni ./task_logging papkasida alohida markdown faylda (masalan, task_logging_1.md, task_logging_2 kabi nomlangan fayllarda) yozib, uni izohlab borishi shart.

#### Misol

Masalan, claudega biror xatoni tuzatish vazifasi topshirildi. Bunda claude:
- Zarur fayllarni o'qiydi
- Dasturning loglarini tekshiradi
- Muammoni qidirib topadi
- Uni hal qilish uchun bir qancha cmd/terminal buyruqlarini bajaradi, kodlar yozadi
- Biror qo'shimcha extension/dasturni o'rnatib ishlatadi.
- va hokazo ishlarni qiladi.

Bu holatda loglash quyidagicha bo'ladi:
- O'qigan fayllarining nomini va nima sababdan bu faylni o'qiganini, o'qish natijasida qanday xulosa qilganini 
- Dasturdagi loglarni o'qiganda bu log ma'lumotlaridan qanday xulosa olganini va bu xulosaga nima sababdan kelganini tushunarli qilib
- Topilgan muammoni sababini tavsiflab, unga nima yechim bo'lishini
- Muammoni hal qilishda bajaradigan cmd/terminal buyruqlarini nima sababdan ishlatganini, har bir buyruqning (va uning har bir parametrlarining) tavsifini
- Muammoni hal qilishda yozgan kodlarini (agar katta hajmda kod yozgan bo'lsa, qaysi faylning qaysi qatorlariga yozganini ko'rsatib)
- Biror qo'shimcha extension/dastur o'rnatib ishlatsa, bu extension/dastur nima ish qilishini, joriy muammoni hal qilishda uning ahamiyati nimaligini
- va boshqa ma'lumotlarni
claude_logging papkasidagi log faylga o'qishga oson ko'rinishda yozadi.
