# موعدي (Mawedi) — Project Progress

> المرجع الرسمي لتطوير المشروع — آخر تحديث: 2026-07-05

---

## المرحلة الحالية: إكمال لوحة المستشفى ✅ — جاهز للانتقال لتطبيق المرضى

---

## ما تم تنفيذه

### لوحة تحكم الإدارة (`/admin`) — المرحلة الثانية

| الميزة | الحالة |
|--------|--------|
| Dashboard احترافي بإحصائيات شاملة + رسوم بيانية (Chart.js) | ✅ |
| صفحات تقارير منفصلة لكل إحصاء (`/admin/reports/*`) | ✅ |
| إدارة المستشفيات (بحث/فلترة/ترتيب/pagination موسّع) | ✅ |
| صفحة إحصائيات لكل مستشفى | ✅ |
| إدارة الاشتراكات (شهري / حسب الاستخدام) | ✅ |
| محفظة المستشفى (إيداع/خصم/تعديل + سجل عمليات) | ✅ |
| الأيام المجانية (إدخال عدد أيام حر) | ✅ |
| إيقاف/تفعيل تلقائي (أمر مجدول يومي) | ✅ |
| سعر كشف الطبيب + سجل تغييرات السعر | ✅ |
| متابعة مستخدمي التطبيق (patients) | ✅ |
| صفحة حجوزات كل مستخدم (مجمّعة حسب الحالة) | ✅ |
| سجل العمليات (Audit Logs) | ✅ |
| إعدادات النظام | ✅ |
| نظام صلاحيات (5 أدوار) | ✅ |
| إدارة مستخدمي الإدارة — إضافة/تعديل/حذف/إيقاف (Super Admin) | ✅ |
| بنية الإشعارات (DB فقط — بدون إرسال) | ✅ |
| CRUD التخصصات + CRUD المستشفيات (من المرحلة الأولى) | ✅ |

### لوحة تحكم المستشفى (`/hospital`)

| الميزة | الحالة |
|--------|--------|
| جميع مزايا المرحلة الأولى (أطباء، جداول، إجازات، تأجيل) | ✅ |
| تعديل سعر الكشف + تسجيل log | ✅ |
| عرض رصيد المحفظة وحالة الاشتراك (قراءة فقط) | ✅ |
| صفحة المحفظة والاشتراك + آخر المعاملات | ✅ |
| إنشاء حجز يدوي من موعد متاح | ✅ |
| إدارة الحجوزات: تأكيد، إلغاء، إكمال، لم يحضر | ✅ |
| تسجيل الدفع للحجز | ✅ |
| خصم رسوم الحجز من المحفظة عند التأكيد (حسب الاستخدام) | ✅ |

---

## الجداول

| الجدول | الوصف |
|--------|--------|
| `admins` | مستخدمو الإدارة + `role`, `is_active`, `last_login_at` |
| `hospitals` | المستشفيات + حقول الاشتراك والفترة المجانية والإيقاف |
| `hospital_wallets` | محفظة كل مستشفى (رصيد، إجمالي إيداعات/خصومات) |
| `wallet_transactions` | سجل حركات المحفظة |
| `doctor_price_logs` | سجل تغيير سعر الكشف |
| `patients` | مستخدمو تطبيق المرضى |
| `system_settings` | إعدادات النظام (key/value) |
| `audit_logs` | سجل العمليات الإدارية |
| `notification_campaigns` | بنية الإشعارات المستقبلية (draft فقط) |
| `bookings` | + `patient_id`, `consultation_price` |
| `doctors` | + `consultation_price` |

### جداول المرحلة الأولى (بدون تغيير جوهري)

`hospital_users`, `specialties`, `hospital_specialty`, `doctor_working_days`, `doctor_working_periods`, `doctor_vacations`, `doctor_slots`, `booking_reschedule_logs`

---

## العلاقات الرئيسية

```
Admin (role-based permissions)
Hospital 1──1 HospitalWallet 1──N WalletTransaction
Hospital 1──N Doctor 1──N DoctorPriceLog
Hospital 1──N Booking N──1 Patient (nullable)
Doctor 1──N Booking
AuditLog ── morphTo ── user / auditable
NotificationCampaign ── morphTo ── created_by
```

---

## Enums الجديدة

| Enum | القيم |
|------|-------|
| `AdminRole` | super_admin, admin, finance, support, reports |
| `SubscriptionType` | monthly, usage_based |
| `SubscriptionStatus` | trial, active, expired, suspended |
| `WalletTransactionType` | deposit, deduction, adjustment, booking_fee |
| `AuditAction` | created, updated, deleted, activated, deactivated, ... |
| `DeactivationReason` | manual, subscription_expired, trial_expired, wallet_empty |
| `NotificationTargetType` | all_hospitals, hospital, all_patients, patient |
| `BookingStatus` | + no_show |

---

## البنية المعمارية

```
app/
├── Enums/           # +7 enums جديدة
├── Models/          # +7 models جديدة
├── Repositories/    # +Patient, AuditLog
├── Services/
│   ├── AuditLogService
│   ├── SystemSettingService
│   ├── HospitalWalletService
│   ├── HospitalSubscriptionService
│   ├── HospitalDeactivationService
│   ├── HospitalStatsService
│   ├── AdminReportService
│   ├── DoctorPriceService
│   └── PatientService
├── Http/Controllers/Admin/
│   ├── HospitalStatsController
│   ├── HospitalWalletController
│   ├── HospitalSubscriptionController
│   ├── DoctorPriceController
│   ├── PatientController
│   ├── AuditLogController
│   └── SettingController
├── Http/Middleware/
│   └── EnsureAdminPermission
├── Console/Commands/
│   └── CheckHospitalDeactivation
└── Policies/        # محدّثة بفحص الصلاحيات
```

---

## الخدمات (Services)

| الخدمة | المسؤولية |
|--------|-----------|
| `AuditLogService` | تسجيل جميع العمليات المهمة + IP |
| `SystemSettingService` | إعدادات النظام مع cache |
| `HospitalWalletService` | محفظة المستشفى والعمليات المالية |
| `HospitalSubscriptionService` | اشتراكات، فترة مجانية، تفعيل/إيقاف |
| `HospitalDeactivationService` | فحص وإيقاف تلقائي |
| `HospitalStatsService` | إحصائيات مستشفى واحد |
| `AdminReportService` | تقارير ورسوم بيانية للوحة الرئيسية |
| `DoctorPriceService` | تغيير سعر الكشف + log |
| `PatientService` | إدارة مستخدمي التطبيق وحجوزاتهم |
| `HospitalService` | موسّع: audit + subscription init |

---

## نظام الصلاحيات

| الدور | الصلاحيات |
|-------|-----------|
| **Super Admin** | كل الصلاحيات |
| **Admin** | مستشفيات، تخصصات، مستخدمين، أطباء، إعدادات، audit |
| **Finance** | محافظ، اشتراكات، تقارير (عرض مستشفيات) |
| **Support** | مستخدمين، حجوزات، عرض مستشفيات، audit |
| **Reports** | تقارير وعرض فقط |

التطبيق: `Admin::hasPermission()` + Policies + `EnsureAdminPermission` middleware

---

## قرارات برمجية مهمة

1. **محفظة منفصلة:** جدول `hospital_wallets` بدلاً من حقل في `hospitals` لدعم سجل العمليات الكامل.
2. **سعر الكشف:** حقل `consultation_price` على `doctors` + جدول `doctor_price_logs` لتتبع التغييرات من الإدارة أو المستشفى.
3. **الإيقاف التلقائي:** أمر `hospitals:check-deactivation` مجدول يومياً عبر `routes/console.php`.
4. **الفترة المجانية:** إدخال عدد أيام حر (ليس قائمة ثابتة) عبر `grantFreeTrial(days)`.
5. **الاشتراك الشهري:** `subscription_ends_at` = `subscription_starts_at` + مدة بالأشهر.
6. **حسب الاستخدام:** رسوم الحجز تُحدد من الإدارة فقط (`usage_fee_per_booking`) وتُخصم من المحفظة عند **تأكيد** الحجز من لوحة المستشفى عبر `deductBookingFee`.
7. **Audit Log:** تسجيل مركزي عبر `AuditLogService` في جميع العمليات الحساسة.
8. **الإشعارات:** جدول `notification_campaigns` للتجهيز المستقبلي — بدون إرسال فعلي.
9. **المريض:** جدول `patients` منفصل مع ربط اختياري في `bookings.patient_id`.
10. **No Show:** حالة حجز جديدة `no_show` في `BookingStatus`.

---

## Migrations الجديدة (2026_07_05)

| الملف | الوصف |
|-------|--------|
| `000001_extend_admins_table` | role, is_active, last_login_at |
| `000002_extend_hospitals_subscription` | حقول الاشتراك والفترة المجانية |
| `000003_create_hospital_wallets_tables` | محفظة + عمليات |
| `000004_add_doctor_consultation_price` | سعر الكشف + logs |
| `000005_create_patients_table` | مستخدمي التطبيق |
| `000006_create_settings_audit_notifications_tables` | إعدادات + audit + إشعارات |

---

## بيانات الدخول التجريبية

| الدور | البريد | كلمة المرور | الرابط |
|-------|--------|-------------|--------|
| Super Admin | `admin@mawedi.com` | `password` | `/admin/login` |
| Finance | `finance@mawedi.com` | `password` | `/admin/login` |
| المستشفى | `hospital_test@mawedi.com` | `password` | `/hospital/login` |
| مريض | `0911111111` | `password` | ⏳ تطبيق لاحق |

---

## أوامر التشغيل

```bash
php artisan migrate --seed
php artisan storage:link
php artisan serve
php artisan hospitals:check-deactivation   # يدوي
php artisan schedule:work                  # للجدولة المحلية
```

---

## ملاحظات للمراحل القادمة

1. **تطبيق المرضى:** API/Web للتسجيل والحجز + ربط `patient_id` تلقائياً.
2. **إرسال الإشعارات:** FCM/SMS بناءً على `notification_campaigns`.
3. **Feature tests:** تغطية الصلاحيات والمحفظة والاشتراكات والحجوزات.
4. **Queue:** جدولة توليد المواعيد كـ Job يومي.

---

## حالة الاختبار

- ✅ Migrations تعمل
- ✅ Seeders تعمل
- ✅ Routes مسجلة (52 مسار admin + 48 مسار hospital)
- ✅ أمر الإيقاف التلقائي يعمل
- ⏳ Feature tests (لم تُنفَّذ بعد)
