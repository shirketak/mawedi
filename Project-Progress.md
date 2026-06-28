# موعدي (Mawedi) — Project Progress

> المرجع الرسمي لتطوير المشروع — آخر تحديث: 2026-06-28

---

## المرحلة الحالية: المرحلة الأولى ✅

تنفيذ لوحة تحكم الإدارة ولوحة تحكم المستشفى (بدون تطبيق المرضى).

---

## ما تم تنفيذه

### لوحة تحكم الإدارة (`/admin`)

| الميزة | الحالة |
|--------|--------|
| تسجيل الدخول (guard منفصل) | ✅ |
| Dashboard بإحصائيات | ✅ |
| CRUD المستشفيات (إضافة/تعديل/حذف/تفعيل/إيقاف) | ✅ |
| إنشاء حساب مستشفى تلقائياً عند الإضافة | ✅ |
| CRUD التخصصات الطبية (إضافة/تعديل/حذف/تفعيل/إيقاف) | ✅ |
| بحث + فلترة + Pagination | ✅ |
| رفع الشعار والأيقونات | ✅ |

### لوحة تحكم المستشفى (`/hospital`)

| الميزة | الحالة |
|--------|--------|
| تسجيل دخول مستقل لكل مستشفى | ✅ |
| Dashboard مميز بإحصائيات وحجوزات قادمة | ✅ |
| عزل بيانات المستشفى (Policies + hospital_id) | ✅ |
| تعديل بيانات التواصل / اللوجو / الموقع / العنوان | ✅ |
| اختيار التخصصات من قائمة الإدارة (إضافة/حذف) | ✅ |
| CRUD الأطباء | ✅ |
| جدول عمل أسبوعي متعدد الفترات لكل يوم | ✅ |
| مدة كشف بالدقائق (إدخال حر) | ✅ |
| توليد المواعيد (doctor_slots) تلقائياً | ✅ |
| إجازات الطبيب (يوم كامل / استثنائية) | ✅ |
| تأجيل حجوزات يوم معين تلقائياً | ✅ |
| سجل تأجيل المواعيد (booking_reschedule_logs) | ✅ |
| عرض الحجوزات (بحث + فلترة) | ✅ |
| إنشاء الحجز من تطبيق المرضى | ⏳ مرحلة لاحقة |

---

## الجداول

| الجدول | الوصف |
|--------|--------|
| `admins` | مستخدمو لوحة الإدارة |
| `hospitals` | المستشفيات (soft delete) |
| `hospital_users` | حسابات دخول المستشفيات |
| `specialties` | التخصصات الطبية (soft delete) |
| `hospital_specialty` | ربط المستشفى بالتخصصات |
| `doctors` | الأطباء (soft delete) |
| `doctor_working_days` | أيام عمل الطبيب |
| `doctor_working_periods` | فترات العمل لكل يوم |
| `doctor_vacations` | إجازات الطبيب |
| `doctor_slots` | المواعيد المولّدة تلقائياً |
| `bookings` | الحجوزات (جاهزة للمرحلة القادمة) |
| `booking_reschedule_logs` | سجل نقل الحجوزات |

---

## العلاقات الرئيسية

```
Hospital 1──N HospitalUser
Hospital N──M Specialty (hospital_specialty)
Hospital 1──N Doctor
Doctor N──1 Specialty
Doctor 1──N DoctorWorkingDay 1──N DoctorWorkingPeriod
Doctor 1──N DoctorVacation
Doctor 1──N DoctorSlot
Hospital 1──N Booking
Doctor 1──N Booking
DoctorSlot 1──0..1 Booking
Hospital 1──N BookingRescheduleLog
```

---

## البنية المعمارية

```
app/
├── Enums/           # LibyaGovernorate, DayOfWeek, BookingStatus, PaymentStatus, SlotStatus, VacationType
├── Traits/          # HasUuid
├── Helpers/         # FileUploader
├── Models/
├── Repositories/
│   ├── Contracts/
│   └── Eloquent/
├── Services/
├── Http/
│   ├── Controllers/Admin/
│   ├── Controllers/Hospital/
│   ├── Middleware/
│   └── Requests/
└── Policies/
```

### الخدمات (Services)

| الخدمة | المسؤولية |
|--------|-----------|
| `HospitalService` | إدارة المستشفيات + إنشاء حساب المستخدم |
| `HospitalProfileService` | تحديث بيانات المستشفى من لوحته |
| `SpecialtyService` | إدارة التخصصات (الإدارة) |
| `HospitalSpecialtyService` | ربط التخصصات بالمستشفى |
| `DoctorService` | CRUD الأطباء |
| `DoctorScheduleService` | جدول العمل + التأجيل |
| `DoctorSlotService` | توليد/مزامنة المواعيد (8 أسابيع قادمة) |
| `DoctorVacationService` | الإجازات + حظر المواعيد |
| `BookingRescheduleService` | نقل الحجوزات بالترتيب + التسجيل |
| `BookingService` | عرض الحجوزات والسجلات |

### Guards المصادقة

| Guard | الجدول | المسار |
|-------|--------|--------|
| `admin` | `admins` | `/admin/*` |
| `hospital` | `hospital_users` | `/hospital/*` |

---

## الملفات الجديدة (ملخص)

- **Migrations:** 12 ملف في `database/migrations/2026_06_28_*`
- **Models:** 11 model
- **Repositories:** 6 interfaces + 6 implementations
- **Services:** 10 services
- **Controllers:** 12 controller
- **Requests:** 13 form request
- **Policies:** 4 policies
- **Middleware:** `EnsureAdmin`, `EnsureHospitalUser`
- **Views:** layouts + admin + hospital (Blade + Bootstrap 5 RTL)
- **Seeders:** `AdminSeeder`, `SpecialtySeeder`, `DemoDataSeeder`

---

## قرارات برمجية مهمة

1. **UUID في الروابط:** جميع الكيانات الرئيسية تستخدم `uuid` كـ route key عبر trait `HasUuid`.
2. **فصل Guards:** الإدارة والمستشفى guards مستقلة تماماً لدعم التوسع المستقبلي.
3. **توليد المواعيد:** عند حفظ جدول العمل أو تغيير مدة الكشف أو إضافة إجازة، يُعاد توليد `doctor_slots` لـ 8 أسابيع قادمة دون المساس بالمواعيد المحجوزة.
4. **تأجيل الحجوزات:** يبحث عن أقرب مواعيد متاحة بعد اليوم المحدد، ينقل الحجوزات بالترتيب، ويسجل تفاصيل كل نقل في `booking_reschedule_logs`.
5. **بيانات المريض في الحجز:** مخزنة مباشرة في `bookings` (patient_name, patient_phone) حتى مرحلة تطبيق المرضى؛ يمكن لاحقاً ربطها بجدول `patients`.
6. **المحافظات:** Enum ثابت `LibyaGovernorate` بقائمة محافظات ليبيا.
7. **الألوان:** `#3DA8C4` / `#2D8FAE` (أساسي)، `#3E9B52` / `#237640` (مستشفى)، `#F59E0B` / `#D97706` (تحذير)، `#F8FBFC` (خلفية).

---

## بيانات الدخول التجريبية (بعد Seed)

| الدور | البريد | كلمة المرور | الرابط |
|-------|--------|-------------|--------|
| الإدارة | `admin@mawedi.ly` | `password` | `/admin/login` |
| المستشفى | `hospital@mawedi.ly` | `password` | `/hospital/login` |

---

## أوامر التشغيل

```bash
php artisan migrate --seed
php artisan storage:link
php artisan serve
```

---

## ملاحظات للمراحل القادمة

1. **تطبيق المرضى:** API/Web لإنشاء الحجوزات، ربط `patient_id`، دفع إلكتروني.
2. **جدول patients:** عند بناء تطبيق المرضى.
3. **إشعارات:** عند تأجيل الحجوزات (SMS/Push).
4. **تقارير:** لوحة إدارة متقدمة.
5. **Queue:** جدولة توليد المواعيد كـ Job يومي.
6. **Admin reschedule logs:** عرض سجل التأجيل من لوحة الإدارة (حالياً من المستشفى فقط).
7. **Factories:** لاختبارات Feature شاملة.

---

## حالة الاختبار

- ✅ Migrations تعمل على MySQL
- ✅ Seeders تعمل
- ✅ Routes مسجلة
- ⏳ Feature tests (لم تُنفَّذ بعد)

---

## Responsive UI (2026-06-28)

- ملف CSS مشترك: `public/css/dashboard.css` (Mobile First)
- Sidebar → Offcanvas على الشاشات `< 992px`
- جداول داخل `table-responsive` مع scroll أفقي محلي فقط
- أزرار وحقول إدخال بحجم لمس مريح (min-height: 2.75rem)
- Grid متدرج: `col-12` → `col-sm-6` → `col-md-*` → `col-lg-*`
- أعمدة جداول مخفية تدريجياً على الشاشات الصغيرة (`d-none d-md-table-cell`)
- Partial: `partials/page-toolbar`, `partials/admin-nav`, `partials/hospital-nav`
