# Notification System

Laravel tabanlı bir bildirim yönetim sistemi. Bu sistem, mesajların kuyruk mekanizması ile işlenmesini ve harici servislere gönderilmesini sağlar.

##  Gereksinimler

- Docker ve Docker Compose
- Git

##  Kurulum

### 1. Projeyi Klonlayın

```bash
git clone https://github.com/halilomergurgan/notification-system-.git
cd notification-system-
```

### 2. Ortam Değişkenlerini Ayarlayın

```bash
cp .env.example .env
```

`.env` dosyasını açın ve aşağıdaki değerleri kontrol edin:

```env
DB_CONNECTION=mysql
DB_HOST=db
DB_PORT=3306
DB_DATABASE=sms_notification
DB_USERNAME=sms_user
DB_PASSWORD=secret

REDIS_HOST=redis
REDIS_PASSWORD=null
REDIS_PORT=6379

QUEUE_CONNECTION=redis
```

### 3. Docker Container'ları Başlatın

```bash
docker-compose up -d
```

Bu komut aşağıdaki servisleri başlatacaktır:
- **sms_app**: PHP-FPM (Laravel uygulaması)
- **sms_webserver**: Nginx web sunucusu
- **sms_db**: MySQL veritabanı
- **sms_redis**: Redis (Cache ve Queue için)
- **sms_queue**: Queue Worker (Arka plan işlemleri)

### 4. Composer Bağımlılıklarını Yükleyin

```bash
docker exec -it sms_app bash
composer install
exit
```

### 5. Uygulama Anahtarını Oluşturun

```bash
docker exec -it sms_app php artisan key:generate
```

### 6. Veritabanı Migrasyonlarını Çalıştırın

```bash
docker exec -it sms_app php artisan migrate
```

### 7. Test Verilerini Yükleyin

```bash
docker exec -it sms_app php artisan db:seed
```

## Docker Container Yönetimi

### Container'a Giriş

```bash
# PHP Container'a giriş
docker exec -it sms_app bash

# MySQL Container'a giriş
docker exec -it sms_db mysql -u sms_user -psecret sms_notification
```

### Container'ları Durdurma/Başlatma

```bash
# Durdurma
docker-compose down

# Başlatma
docker-compose up -d

# Logları görüntüleme
docker-compose logs -f
```

## 🔧 Artisan Komutları

### Mesaj Kuyruğunu Başlatma

```bash
docker exec -it sms_app php artisan messages:dispatch
```

Bu komut, bekleyen mesajları işlemek için kuyruk işlemini başlatır.

### Queue Worker'ı Çalıştırma

```bash
docker exec -it sms_app php artisan queue:work
```

### Webhook Test

Test amaçlı webhook kullanımı için:

1. [https://webhook.site](https://webhook.site) adresinden bir webhook URL'i alın
2. Edit butonuna tıklayıp Content kısmına şu response'u girin:
```json
{
    "message": "Accepted",
    "messageId": "test-123",
    "status": "sent"
}

##  API Kullanımı

### API Dokümantasyonu

Swagger dokümantasyonuna erişim:
```
http://localhost:8080/api/documentation
```

### Mesajları Listeleme

**Query Parametreleri:**
- `status` (opsiyonel): Mesaj durumuna göre filtreleme. Değerler: `pending`, `processing`, `sent`, `cancelled`, `failed`
- `page` (opsiyonel): Sayfa numarası (varsayılan: 1)
- `per_page` (opsiyonel): Sayfa başına kayıt sayısı (varsayılan: 20)
- 
```bash
GET http://localhost:8080/api/messages?status={status}
```

Örnek Response:
```json
{
    "data": [
        {
            "id": 1,
            "number": "+905551234567",
            "message": "Test mesajı",
            "status": "pending",
            "sent_at": null,
            "created_at": "2025-08-13 14:29:00"
        },
        {
            "id": 2,
            "number": "+905559876543",
            "message": "Hoş geldiniz",
            "status": "pending",
            "sent_at": null,
            "created_at": "2025-08-13 14:35:00"
        }
    ],
    "links": {
        "first": "http://localhost:8080/api/messages?page=1",
        "last": "http://localhost:8080/api/messages?page=1",
        "prev": null,
        "next": null
    },
    "meta": {
        "current_page": 1,
        "from": 1,
        "last_page": 1,
        "path": "http://localhost:8080/api/messages",
        "per_page": 20,
        "to": 2,
        "total": 2
    }
}
```

## 🧪 Test Çalıştırma

```bash
# Tüm testleri çalıştır
docker exec -it sms_app php artisan test

```

##  Sistem Mimarisi

### Mesaj İşleme Akışı

1. **Mesaj Oluşturma**: Sistem, `message_queues` tablosundakileri işlemeye başlayacaktır.
2. **Kuyruk İşleme**: `ProcessMessageQueueJob` job'ı düzenli olarak çalışır
3. **Batch İşleme**: Her seferinde 2 mesaj işlenir
4. **Durum Güncelleme**:
    - `pending` → `dispatched` → `sent/failed`
5. **Yeniden Deneme**:
    - `dispatched` durumunda mesaj varsa: 5 saniye sonra
    - Yoksa: 30 saniye sonra

### Cache Temizleme

```bash
docker exec -it sms_app php artisan cache:clear
docker exec -it sms_app php artisan config:clear
docker exec -it sms_app php artisan route:clear
```

### Swagger Dokümantasyonunu Güncelleme

```bash
docker exec -it sms_app php artisan l5-swagger:generate
```

## Notlar

- Proje `http://localhost:8080` adresinde çalışır
- MySQL veritabanına `localhost:3306` üzerinden erişebilirsiniz
- Redis'e `localhost:6379` üzerinden erişebilirsiniz
- Queue worker php artisan queue:work olarak başlatılır ve sürekli çalışır

### Veritabanı bağlantı hatası

1. `.env` dosyasındaki veritabanı bilgilerini kontrol edin
2. MySQL container'ının çalıştığından emin olun
3. Container'ları yeniden başlatın:

```bash
docker-compose down
docker-compose up -d
```

### Permission hatası

```bash
docker exec -it sms_app bash
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
exit
```
