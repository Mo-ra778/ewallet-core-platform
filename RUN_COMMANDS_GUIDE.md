# 🚀 الدليل الشامل لأوامر التشغيل والبيئة التطويرية | E-Wallet Core Platform & Mobile App

> **إعداد المهندس:** م. محمد رشاد الإدريسي (Mohammed Rashad Al-Edrisi)
> **المشروع:** منظومة المحفظة الإلكترونية والخدمات النقدية والمصرفية (وافي باي - Wafi Pay)
> **التاريخ:** 2026

---

## 📌 نظرة عامة على هيكل المنظومة

المنظومة تتكون من جزأين رئيسيين:

1. **الخلفية ولوحات التحكم (Backend & Admin/Agent Portals):** مبنية بـ Laravel (PHP) + Blade + PostgreSQL/SQLite + Redis + SMTP.
2. **تطبيق الموبايل (Mobile App):** مبني بـ React Native / Expo (TypeScript) للعملاء على Android و iOS.

---

## 📱 أولاً: أوامر تشغيل تطبيق الموبايل (Expo / React Native)

المسار المحلي للمشروع: `D:\wallet-app`

### 1. المتطلبات المسبقة:

- تثبيت [Node.js](https://nodejs.org/) (إصدار LTS 18 أو 20+).
- تطبيق **Expo Go** مثبت على هاتفك المحمول من متجر [Google Play](https://play.google.com/store/apps/details?id=host.exp.exponent) أو [App Store](https://apps.apple.com/app/expo-go/id982107779).

---

### 2. تثبيت الحزم (Dependencies):

افتح موجه الأوامر (PowerShell / Terminal) داخل مجلد التطبيق:

```powershell
cd D:\wallet-app
npm install
```

*(أو إذا كنت تستخدم yarn)*:

```powershell
yarn install
```

---

### 3. أوامر تشغيل التطبيق عبر Expo:

#### 🟢 التشغيل القياسي (Standard Expo Start):

```powershell
npx expo start
```

أو عبر اختصار npm:

```powershell
npm start
```

#### ⚡ التشغيل مع مسح الكاش (في حال حدوث أخطاء أو تعليق):

```powershell
npx expo start -c
```

*(المفتاح `-c` يقوم بمسح Metro Bundler Cache لضمان تحميل أحدث التعديلات)*.

#### 🌐 التشغيل عبر نفق الإنترنت (Tunnel Mode):

> **مهم جداً:** استخدم هذا الخيار إذا كان هاتفك والكمبيوتر غير متصلين بنفس شبكة الـ Wi-Fi، أو عند حظر الاتصال المحلي بجدار الحماية:

```powershell
npx expo start --tunnel
```

---

### 4. خيارات التشغيل المباشر لأجهزة معينة:

* **التشغيل على محاكي الأندرويد (Android Emulator):**
  ```powershell
  npm run android
  # أو
  npx expo start --android
  ```
* **التشغيل في المتصفح كـ Web App:**
  ```powershell
  npm run web
  # أو
  npx expo start --web
  ```

---

### 5. كيفية ربط هاتفك والبدء بالمعاينة:

1. بعد تنفيذ `npx expo start`، سيظهر لك **رمز QR Code** كبير في التيرمينال، ورابط في المتصفح.
2. **على أجهزة Android:** افتح تطبيق **Expo Go** واضغط **Scan QR Code** وامسح الرمز من شاشة الكمبيوتر.
3. **على أجهزة iPhone:** افتح تطبيق **الكاميرا الأساسي** في الآيفون ووجهه نحو الـ QR Code واضغط على الإشعار الأصفر لفتحه في Expo Go.

---

### 6. اختصارات لوحة المفاتيح المفيدة أثناء تشغيل Expo:

- اضغط حرف `r` في التيرمينال: لإعادة تحميل التطبيق (Reload).
- اضغط حرف `m` في التيرمينال: لفتح قائمة المطور (Developer Menu).
- اضغط حرف `c` في التيرمينال: لمسح الكاش فوراً.
- اضغط `Ctrl + C`: لإيقاف السيرفر.

---

## 🖥️ ثانياً: أوامر تشغيل الـ Backend (Laravel & Web Portals)

المسار المحلي للمشروع: `D:\WALLET\ewallet-backend`

### 1. تثبيت حزم الـ PHP والـ JavaScript:

```powershell
cd D:\WALLET\ewallet-backend
composer install
npm install
```

---

### 2. تشغيل السيرفر المحلي مع إمكانية الوصول من الجوال والشبكة المحلية:

الوضع الافتراضي (`php artisan serve`) يستمع فقط على جهازك (`127.0.0.1`). لكي يستقبل الاتصال من **هاتفك الجوال**، شغّله بالأمر التالي:

```powershell
php artisan serve --host=0.0.0.0 --port=8000
```

> **لماذا `--host=0.0.0.0`؟**
> هذا الأمر يخبر Laravel بالاستماع على جميع كروت الشبكة والـ Wi-Fi في اللابتوب، مما يسمح لأي هاتف متصل بنفس شبكة الـ Wi-Fi بالوصول للسيرفر.

* **عنوان السيرفر للجوال (عنوان الـ IP الحالي للابتوب):**👉 `http://192.168.0.40:8000` (أو `http://192.168.0.169:8000`)
* **رابط الـ API في تطبيق الموبايل:**👉 `http://192.168.0.40:8000/api`
* **بوابة الوكيل من جوالك:**👉 `http://192.168.0.40:8000/agent/login`
* **لوحة تحكم الإدارة من جوالك:**
  👉 `http://192.168.0.40:8000/admin/login`

> **💡 تنبيه هام (Windows Firewall):**
> إذا فتحت الرابط من الجوال ولم يستجب، تأكد من الضغط على **Allow Access (السماح)** في نافذة جدار حماية ويندوز التي تظهر عند تشغيل الأمر لأول مرة، أو اسمح للمنفذ `8000` في Windows Firewall.

---

### 3. أوامر قاعدة البيانات والتحديثات (Migrations & Seeds):

```powershell
# ترحيل الجداول بدون حذف البيانات
php artisan migrate

# ترحيل الجداول وإعادة بناء قاعدة البيانات من الصفر مع البيانات التجريبية:
php artisan migrate:fresh --seed
```

---

### 4. أوامر الصيانة ومسح الـ Cache (ضرورية جداً عند تغيير إعدادات .env):

```powershell
# مسح كاش الإعدادات
php artisan config:clear

# مسح كاش المسارات
php artisan route:clear

# مسح كاش القوالب (Blade)
php artisan view:clear

# مسح شامل لجميع أنواع الكاش
php artisan optimize:clear
```

---

### 5. بناء ملفات الواجهات (CSS / JS عبر Vite):

```powershell
# أثناء التطوير المباشر مع التحديث التلقائي:
npm run dev

# لبناء ملفات الإنتاج الجاهزة للرفع:
npm run build
```

---

## 🔄 ثالثاً: ربط تطبيق الموبايل مع الـ Backend (محلياً أو سحابياً)

داخل تطبيق الموبايل، يمكنك التبديل بين السيرفرين بسهولة:

### 1. السيرفر السحابي (Cloud Production - Vercel):

```text
https://ewallet-core-platform.vercel.app/api
```

*(يعمل من أي مكان في العالم ومربوط بقاعدة بيانات Neon PostgreSQL السحابية)*.

### 2. السيرفر المحلي (Local Dev):

إذا كنت تشغل `php artisan serve` محلياً:

- في محاكي الأندرويد (Android Emulator) استخدم: `http://10.0.2.2:8000/api`
- على هاتفك الحقيقي (عبر Wi-Fi) استخدم IP جهازك، مثلاً: `http://192.168.1.X:8000/api`

---

## ☁️ رابعاً: أوامر التزامن والرفع إلى GitHub

عند إجراء أي تعديلات وتريد حفظها ورفعها للسحابة:

```powershell
cd D:\WALLET

# التحقق من حالة الملفات المعدلة
git status

# إضافة التعديلات
git add .

# كتابة رسالة الحفظ
git commit -m "feat: your update description"

# رفع التعديلات لـ GitHub
git push origin main
```

---

*تم إنشاء هذا الدليل ليكون مرجعاً تقنياً سريعاً وشاملاً لجميع أفراد فريق العمل.*
