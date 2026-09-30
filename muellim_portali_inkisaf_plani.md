# Khazar University — Müəllim Portalı İnkişaf Və İcra Planı (v2.0)

> [!NOTE]
> Bu sənəd **Müəllim və Professor Kabinetinin (Teacher Portal)** rəqəmsal imkanlarını Harvard, MIT Canvas, Oxford Nexus, ADA Canvas və ADNSU Unibook standartlarına qaldırmaq üçün hazırlanmış rəsmi strategiya və icra planıdır.

---

## 📌 PDF Sənəd Keçidləri

- 📄 **Yüklənə Bilən Rəsmi PDF Sənədi**: [muellim_portali_inkisaf_plani.pdf](file:///c:/Users/MSI/university/muellim_portali_inkisaf_plani.pdf)
- 📁 **Artifakt PDF Faylı**: [muellim_portali_inkisaf_plani.pdf](file:///C:/Users/MSI/.gemini/antigravity-ide/brain/a072d9ba-df75-42ec-8d6a-54a929064a71/muellim_portali_inkisaf_plani.pdf)

---

## 🚀 Mərhələli İcra Planı və Funksional Modullar

### 📅 **Mərhələ 1: Live Dərs Cədvəli Grid-i & Dinamik QR Davamiyyət Seansı**
- **İnteraktiv Həftəlik Cədvəl Grid-i**: Müəllimin dərslərinin günlər, saatlar, auditoriyalar və korpuslar (Neftçilər, Bəşir Səfəroğlu) üzrə vizual təqvimi.
- **Canlı QR Davamiyyət Kioskı**: Dərs vaxtı başladığı an müəllim ekranında dinamik QR-kod seansı açılır. Tələbələr mobil telefonla skan edərək anında "İştirak edir" kimi qeydə alınır.
- **Seanslı Elektron Jurnal**: Hər bir dərs seansı üzrə manual "Qayıb / İştirak edir / Üzrlü" statuslarının idarə edilməsi (`AttendanceController`).

---

### 📚 **Mərhələ 2: LMS Tədris Hub, Tapşırıqlar (Assignments) və Dərs Materialları Bankı**
- **Tapşırıq (Assignment) Portalı**: Fənn üzrə ev tapşırıqları, layihələr və laboratoriya işlərinin yerləşdirilməsi, son təhvil tarixinin (deadline) təyin edilməsi.
- **Onlayn Qiymətləndirmə & Feedback**: Tələbələrin göndərdiyi Word/PDF fayllarını brauzerdə açaraq tələbəyə bal və şəxsi rəy (feedback) göndərilməsi.
- **15 Həftəlik Material Bankı**: Mühazirə slaydları, dərs PDF-ləri və video keçidlərin sistemə yüklənməsi.
- **Təcili Qrup Elanları**: Müəllimin qrupa təcili elan göndərməsi (email və in-app bildiriş inteqrasiyası ilə).

---

### 📊 **Mərhələ 3: Sinif Qiymətləndirmə Analitikası, Statistik Qrafiklər və Excel İdxal/İxrac**
- **Sinif Qiymət Statistikası (Analytics Dashboard)**: Qrup üzrə orta bal (Average Score), keçmə faizi (Pass Rate), ən yüksək/ən aşağı nəticə və hərf qiyməti paylanması (A, B, C, D, F) diaqramları.
- **Akademik Risk Xəbərdarlığı**: Qayıb sınırı (25%) təhlükəsində olan və ya dərslərdən geridə qalan tələbələrin avtomatik `Warning` siyahısı.
- **Excel / CSV Toplu İdxal və İxrac (Bulk Import/Export)**: Qiymət və davamiyyət cədvəlini Excel faylı kimi yükləyib, tək kliklə avtomatik import etmək.

---

### 🤝 **Mərhələ 4: Tələbə Məsləhətləşmə Saatları (Office Hours) & Apellyasiya Meneceri**
- **Office Hours Booking Sistemi**: Müəllimin fərdi məsləhətləşmə vaxtlarını təyin etməsi (Məs: Çərşənbə 14:00-16:00) və tələbələrin portal vasitəsilə vaxt bron etməsi.
- **Çoxmərhələli Apellyasiya Kabineti**: Tələbələrin qiymət etirazlarına müəllim tərəfindən rəy yazılması, bal tənzimlənməsi və ya Apellyasiya Komissiyasına yönləndirilməsi.

---

### 🔬 **Mərhələ 5: Magistr Dissertasiyası (Tezis) Rəhbərliyi & Müəllimin Elmi Profil Kabineti**
- **Magistrant Tezis Nəzarəti**: Elmi rəhbərlik edilən magistrantların dissertasiya fəsillərinin (Chapter 1, Chapter 2), plagiatlıq faizinin və müdafiəyə buraxılış statusunun idarə edilməsi.
- **Müəllimin Elmi Profil Portalı**: Google Scholar, Scopus, Web of Science inteqrasiyası ilə müəllimin nəşr etdirdiyi elmi məqalələrin və citation göstəricilərinin nümayişi.

---

## 📊 İcra Müddəti Və Prioritet Cədvəli

| Mərhələ | Modul Adı | Əsas İcra Sferası | Prioritet |
|---|---|---|---|
| **Mərhələ 1** | Dərs Cədvəli Grid & Canlı QR Davamiyyət | `AttendanceController`, `StudentServicesController` | 🔴 **YÜKSƏK** |
| **Mərhələ 2** | LMS Tapşırıqlar & Material Hub | `LmsController`, Assignment Submissions | 🔴 **YÜKSƏK** |
| **Mərhələ 3** | Sinif Analitikası & Excel Import/Export | `GradeDistributionController`, Chart.js | 🟡 **ORTA** |
| **Mərhələ 4** | Office Hours Booking & Apellyasiya | `StudentServicesController`, Booking | 🟡 **ORTA** |
| **Mərhələ 5** | Magistr Tezis & Scopus/Scholar Profil | `StudentThesis`, Research Module | 🟢 **STRATEGİK** |
