# 📱 توثيق واجهات برمجة التطبيقات (E-Wallet API Contract)
### مخصص لمطور تطبيق Flutter والمطورين الخارجيين

الـ Base URL: `http://127.0.0.1:8000/api`

---

## 1. الترويسات الإلزامية (Headers)

```http
Content-Type: application/json
Accept: application/json
Authorization: Bearer <JWT_TOKEN>  (في الروابط المحمية)
```

---

## 2. المصادقة والحسابات (Authentication)

### أ. تسجيل حساب جديد (Register)
* **الرابط:** `POST /api/auth/register`
* **الحالة الافتراضية:** يُنشأ الحساب بحالة `pending` (بانتظار موافقة الإدارة).
* **الـ Request Body:**
```json
{
  "full_name": "أحمد محمد",
  "phone": "0501234567",
  "email": "ahmed@email.com",
  "password": "password123"
}
```
* **الـ Response (201 Created):**
```json
{
  "success": true,
  "message": "تم إنشاء الحساب بنجاح، حسابك قيد المراجعة بانتظار موافقة الإدارة.",
  "data": {
    "user": {
      "id": "9d5e3f21-7b8a-4c12-98ab-3f56d9812345",
      "full_name": "أحمد محمد",
      "phone": "0501234567",
      "email": "ahmed@email.com",
      "status": "pending",
      "balance": 0,
      "created_at": "2026-08-22T04:00:00.000000Z"
    }
  }
}
```

---

### ب. تسجيل الدخول (Login)
* **الرابط:** `POST /api/auth/login`
* **الـ Request Body:**
```json
{
  "phone": "0501234567",
  "password": "password123"
}
```
* **الـ Response (200 OK):**
```json
{
  "success": true,
  "message": "تم تسجيل الدخول بنجاح.",
  "data": {
    "token": "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9...",
    "token_type": "Bearer",
    "user": {
      "id": "9d5e3f21-7b8a-4c12-98ab-3f56d9812345",
      "full_name": "أحمد محمد",
      "phone": "0501234567",
      "email": "ahmed@email.com",
      "status": "active",
      "balance": 1500.00
    }
  }
}
```

---

### ج. الملف الشخصي (Profile)
* **الرابط:** `GET /api/auth/profile`
* **الـ Headers:** `Authorization: Bearer <TOKEN>`
* **الـ Response (200 OK):**
```json
{
  "success": true,
  "message": "تم جلب بيانات الحساب بنجاح.",
  "data": {
    "user": {
      "id": "9d5e3f21-7b8a-4c12-98ab-3f56d9812345",
      "full_name": "أحمد محمد",
      "phone": "0501234567",
      "email": "ahmed@email.com",
      "status": "active",
      "balance": 1500.00,
      "created_at": "2026-08-22T04:00:00.000000Z"
    }
  }
}
```

---

## 3. العمليات المالية والمحفظة (Wallet Operations)

> **ملاحظة:** تتطلب هذه الروابط أن يكون حساب المستخدم نشطاً ومفعلاً (`active`). إذا كان الحساب `pending` أو `suspended` سيُرجع السيرفر كود `403 Forbidden` برسالة توضيحية.

### أ. استعلام الرصيد (Balance)
* **الرابط:** `GET /api/wallet/balance`
* **الـ Headers:** `Authorization: Bearer <TOKEN>`
* **الـ Response (200 OK):**
```json
{
  "success": true,
  "message": "تم استرجاع الرصيد بنجاح.",
  "data": {
    "balance": 1500.00,
    "currency": "YER",
    "status": "active",
    "is_active": true
  }
}
```

---

### ب. تحويل أموال لمستخدم آخر (Transfer)
* **الرابط:** `POST /api/wallet/transfer`
* **الـ Headers:** `Authorization: Bearer <TOKEN>`
* **الـ Request Body:**
```json
{
  "receiver_phone": "777222333",
  "amount": 250.00,
  "currency": "SAR",
  "description": "سداد فاتورة تسوق"
}
```
* **الـ Response (200 OK):**
```json
{
  "success": true,
  "message": "تم التحويل بنجاح.",
  "data": {
    "transaction_id": "8f12a34b-5c67-89de-01fa-23bc45de6789",
    "amount": 250.00,
    "currency": "SAR",
    "recipient_name": "خالد عبد الله",
    "recipient_phone": "777222333",
    "new_balance": 1250.00,
    "created_at": "2026-08-22T04:15:00.000000Z"
  }
}
```

---

### ج. كشف حساب وسجل العمليات (Transaction History)
* **الرابط:** `GET /api/wallet/transactions?page=1`
* **الـ Headers:** `Authorization: Bearer <TOKEN>`
* **الـ Response (200 OK):**
```json
{
  "success": true,
  "message": "تم جلب سجل العمليات بنجاح.",
  "data": {
    "current_page": 1,
    "data": [
      {
        "id": "8f12a34b-5c67-89de-01fa-23bc45de6789",
        "type": "transfer",
        "amount": "250.00",
        "currency": "SAR",
        "status": "completed",
        "description": "تحويل إلى خالد عبد الله (777222333) - سداد فاتورة",
        "created_at": "2026-08-22T04:15:00.000000Z"
      }
    ],
    "total": 1
  }
}
```

---

## 4. نظام الإشعارات واستقبال أكواد الـ OTP (Notifications)

### أ. جلب قائمة الإشعارات
* **الرابط:** `GET /api/notifications`
* **الـ Headers:** `Authorization: Bearer <TOKEN>`
* **الـ Response (200 OK):**
```json
{
  "success": true,
  "message": "تم جلب الإشعارات بنجاح.",
  "data": {
    "data": [
      {
        "id": "1a2b3c4d-5e6f-7a8b-9c0d-1e2f3a4b5c6d",
        "title": "رمز التحقق للسحب النقدي (OTP)",
        "message": "طلب سحب نقدي بمبلغ 100.00 SAR عبر الوكيل. رمز التأكيد الخاص بك هو: [ 849201 ]. ينتهي خلال 5 دقائق.",
        "type": "otp",
        "is_read": false,
        "created_at": "2026-08-22T04:20:00.000000Z"
      }
    ]
  }
}
```

---

### ب. عدد الإشعارات غير المقروءة (Unread Count)
* **الرابط:** `GET /api/notifications/unread-count`
* **الـ Headers:** `Authorization: Bearer <TOKEN>`
* **الـ Response (200 OK):**
```json
{
  "success": true,
  "message": "تم جلب عدد الإشعارات غير المقروءة.",
  "data": {
    "unread_count": 3
  }
}
```

---

### ج. تحديد إشعار كمقروء (Mark as Read)
* **الرابط:** `POST /api/notifications/{id}/read`
* **الـ Headers:** `Authorization: Bearer <TOKEN>`
* **الـ Response (200 OK):**
```json
{
  "success": true,
  "message": "تم تعيين الإشعار كمقروء.",
  "data": {
    "id": "1a2b3c4d-5e6f-7a8b-9c0d-1e2f3a4b5c6d",
    "is_read": true
  }
}
```
