<?php

namespace Database\Seeders;

use App\Models\AcademicTerm;
use App\Models\ActivityLog;
use App\Models\AdmissionsApplication;
use App\Models\ApplicationDocument;
use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use App\Models\AttendanceRecord;
use App\Models\CampusBuilding;
use App\Models\Course;
use App\Models\CourseAnnouncement;
use App\Models\CourseMaterial;
use App\Models\CourseSection;
use App\Models\Department;
use App\Models\Enrollment;
use App\Models\Event;
use App\Models\Faculty;
use App\Models\GradeComponent;
use App\Models\NewsArticle;
use App\Models\Permission;
use App\Models\Program;
use App\Models\ResearchPaper;
use App\Models\Role;
use App\Models\StudentAppeal;
use App\Models\StudentGradeItem;
use App\Models\StudentThesis;
use App\Models\SystemNotification;
use App\Models\TuitionInvoice;
use App\Models\User;
use App\Services\GradingService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Permissions Definition
        $permissions = [
            ['name' => 'system.all', 'group_name' => 'system', 'label_az' => 'Tam Sistem Nəzarəti', 'description' => 'Sistemin bütün parametrlərinə tam çıxış'],
            ['name' => 'users.manage', 'group_name' => 'users', 'label_az' => 'İstifadəçilərin İdarəsi', 'description' => 'Hesabların yaradılması və redaktəsi'],
            ['name' => 'roles.manage', 'group_name' => 'system', 'label_az' => 'Rollar və İcazələr', 'description' => 'İcazə matrisinin tənzimlənməsi'],
            ['name' => 'academic.manage', 'group_name' => 'academic', 'label_az' => 'Akademik Struktur İdarəsi', 'description' => 'Məktəb, kafedra, ixtisas və fənlərin idarəsi'],
            ['name' => 'academic.teach', 'group_name' => 'academic', 'label_az' => 'Dərs Tədrisi və Materiallar', 'description' => 'Sillabus və dərsliklərin yüklənməsi'],
            ['name' => 'grading.manage', 'group_name' => 'grading', 'label_az' => 'Qiymətləndirmə və Jurnal', 'description' => 'Tələbələrin aralıq və final ballarının daxil edilməsi'],
            ['name' => 'attendance.record', 'group_name' => 'grading', 'label_az' => 'Davamiyyət Qeydiyyatı', 'description' => 'Dərslərdə iştirakın qeyd olunması'],
            ['name' => 'courses.enroll', 'group_name' => 'student', 'label_az' => 'Fənn Seçimi və Qeydiyyat', 'description' => 'Semestr fənlərinin seçilməsi'],
            ['name' => 'schedule.view', 'group_name' => 'student', 'label_az' => 'Dərs Cədvəlinə Baxış', 'description' => 'Həftəlik dərs və imtahan cədvəli'],
            ['name' => 'grades.view', 'group_name' => 'student', 'label_az' => 'Transkript və Ballar', 'description' => 'Fərdi qiymət cədvəli və ÜOMG (GPA)'],
            ['name' => 'admissions.manage', 'group_name' => 'admissions', 'label_az' => 'Qəbul Müraciətlərinin Yoxlanması', 'description' => 'Ərizələrin qəbulu, müsahibə təyini və təsdiq'],
            ['name' => 'admissions.apply', 'group_name' => 'admissions', 'label_az' => 'Onlayn Ərizə Təqdimatı', 'description' => 'Universitetə sənəd qəbulu'],
            ['name' => 'admissions.track', 'group_name' => 'admissions', 'label_az' => 'Müraciət Statusunun İzlənməsi', 'description' => 'Canlı qəbul mərhələləri'],
            ['name' => 'research.publish', 'group_name' => 'research', 'label_az' => 'Elmi Məqalə Dərci', 'description' => 'Nəşrlərin və patentlərin əlavə edilməsi'],
            ['name' => 'theses.submit', 'group_name' => 'research', 'label_az' => 'Magistr Dissertasiya Təqdimatı', 'description' => 'Magistr dissertasiyasının mərhələli izlənməsi'],
            ['name' => 'theses.advise', 'group_name' => 'research', 'label_az' => 'Elmi Rəhbərlik', 'description' => 'Magistrantların tədqiqat işlərinə rəhbərlik'],
            ['name' => 'labs.access', 'group_name' => 'research', 'label_az' => 'Tədqiqat Laboratoriyası Girişi', 'description' => 'Xüsusi elmi laboratoriyalardan istifadə'],
            ['name' => 'tuition.pay', 'group_name' => 'financial', 'label_az' => 'Təhsil Haqqı Ödənişi', 'description' => 'Faktura və qəbzlər'],
            ['name' => 'finances.manage', 'group_name' => 'financial', 'label_az' => 'Maliyyə Hesabatları', 'description' => 'Gəlir və ödəniş auditi'],
            ['name' => 'cms.manage', 'group_name' => 'cms', 'label_az' => 'Xəbər və Məlumat Portalı', 'description' => 'Universitet xəbərləri və elanlar'],
            ['name' => 'campus.manage', 'group_name' => 'campus', 'label_az' => 'Kampus və Xəritə İdarəsi', 'description' => 'Binalar və auditoriyaların qeydiyyatı'],
            ['name' => 'events.rsvp', 'group_name' => 'events', 'label_az' => 'Tədbir Bileti Rezervasiyası', 'description' => 'Tədbirə qeydiyyat və QR bilet'],
        ];

        foreach ($permissions as $p) {
            Permission::create($p);
        }

        // Roles definition
        $roleSystemAdmin = Role::create([
            'name' => 'system_admin',
            'label_az' => 'Sistem Administratoru',
            'description' => 'Bütün sistem modullarına, təhlükəsizlik və verilənlər bazası sazlamalarına tam nəzarət',
            'level' => 100,
        ]);
        $roleSystemAdmin->permissions()->sync(Permission::all());

        $roleAdmin = Role::create([
            'name' => 'admin',
            'label_az' => 'Məktəb & Akademik Admin',
            'description' => 'Məktəb və kafedraların idarəsi, qəbul komissiyası, tədbirlər və xəbərlərin idarəsi',
            'level' => 80,
        ]);
        $roleAdmin->permissions()->sync(
            Permission::whereIn('name', [
                'academic.manage', 'admissions.manage', 'cms.manage', 'campus.manage',
                'finances.manage', 'users.manage', 'events.rsvp', 'schedule.view',
            ])->get()
        );

        $roleTeacher = Role::create([
            'name' => 'teacher',
            'label_az' => 'Professor / Müəllim',
            'description' => 'Dərslərin tədrisi, sillabus, jurnal, imtahan balları və elmi tədqiqatlar',
            'level' => 50,
        ]);
        $roleTeacher->permissions()->sync(
            Permission::whereIn('name', [
                'academic.teach', 'grading.manage', 'attendance.record',
                'research.publish', 'theses.advise', 'schedule.view', 'events.rsvp',
            ])->get()
        );

        $roleMasterStudent = Role::create([
            'name' => 'master_student',
            'label_az' => 'Magistrant / Tədqiqatçı',
            'description' => 'Magistr tədris planı, fənn qeydiyyatı, dissertasiya və elmi nəşr hüquqları',
            'level' => 30,
        ]);
        $roleMasterStudent->permissions()->sync(
            Permission::whereIn('name', [
                'courses.enroll', 'schedule.view', 'grades.view', 'tuition.pay',
                'events.rsvp', 'theses.submit', 'labs.access', 'research.publish',
            ])->get()
        );

        $roleStudent = Role::create([
            'name' => 'student',
            'label_az' => 'Bakalavr Tələbəsi',
            'description' => 'Fənn qeydiyyatı, cədvəl, qiymətlər, təhsil haqqı və rəqəmsal tələbə kartı',
            'level' => 20,
        ]);
        $roleStudent->permissions()->sync(
            Permission::whereIn('name', [
                'courses.enroll', 'schedule.view', 'grades.view', 'tuition.pay', 'events.rsvp',
            ])->get()
        );

        $roleApplicant = Role::create([
            'name' => 'applicant',
            'label_az' => 'Abituriyent / Namizəd',
            'description' => 'Onlayn müraciət, sənəd yükləmə və qəbul statusunun izlənməsi',
            'level' => 10,
        ]);
        $roleApplicant->permissions()->sync(
            Permission::whereIn('name', [
                'admissions.apply', 'admissions.track', 'events.rsvp',
            ])->get()
        );

        // 2. Official Khazar University Schools (Faculties) & Departments
        $schoolsData = [
            [
                'name' => 'School of Science and Engineering (Təbiət Elmləri və Mühəndislik Məktəbi)',
                'slug' => 'school-of-science-and-engineering',
                'code' => 'ENG-SCI',
                'dean_name' => 'Prof. Dr. Fəxrəddin Məmmədov',
                'description' => 'Kompüter elmləri, mühəndislik texnologiyaları, biotibb, neft-qaz və tətbiqi riyaziyyat üzrə aparıcı elmi mərkəz.',
                'icon' => 'laptop-code',
                'color' => 'indigo',
                'established_year' => 1991,
                'students_count' => 2100,
                'professors_count' => 110,
                'departments' => [
                    ['name' => 'Computer Science (Kompüter Elmləri Departamenti)', 'code' => 'CS-DEPT', 'slug' => 'cs-dept', 'head' => 'Prof. Fəxrəddin Məmmədov'],
                    ['name' => 'Life Sciences (Həyat Elmləri Departamenti)', 'code' => 'BIO-DEPT', 'slug' => 'bio-dept', 'head' => 'Dos. İradə Xəlilova'],
                    ['name' => 'Petroleum Engineering (Neft-Qaz Mühəndisliyi)', 'code' => 'PET-DEPT', 'slug' => 'pet-dept', 'head' => 'Dr. Gəray Qasımov'],
                    ['name' => 'Physics and Electronics (Fizika və Elektronika)', 'code' => 'PHY-DEPT', 'slug' => 'phy-dept', 'head' => 'Prof. Nəsib Əhmədov'],
                    ['name' => 'Civil Engineering (İnşaat Mühəndisliyi)', 'code' => 'CIVIL-DEPT', 'slug' => 'civil-dept', 'head' => 'Dr. Rauf Həsənov'],
                    ['name' => 'Mechanical Engineering (Mexanika Mühəndisliyi)', 'code' => 'MECH-DEPT', 'slug' => 'mech-dept', 'head' => 'Dr. Elçin İsmayılov'],
                    ['name' => 'Mathematics (Riyaziyyat Departamenti)', 'code' => 'MATH-DEPT', 'slug' => 'math-dept', 'head' => 'Prof. Hamlet İsaxanlı'],
                    ['name' => 'Architecture and Design (Memarlıq və Dizayn)', 'code' => 'ARCH-DEPT', 'slug' => 'arch-dept', 'head' => 'Dos. Nərgiz Ağayeva'],
                ],
            ],
            [
                'name' => 'School of Economics and Management (İqtisadiyyat və Menecment Məktəbi)',
                'slug' => 'school-of-economics-and-management',
                'code' => 'ECON-MGT',
                'dean_name' => 'Dr. Ceyhun Məmmədov',
                'description' => 'Qlobal maliyyə, biznes idarəçiliyi, beynəlxalq audit və rəqəmsal iqtisadiyyat üzrə qabaqcıl biznes məktəbi.',
                'icon' => 'chart-line',
                'color' => 'amber',
                'established_year' => 1991,
                'students_count' => 1650,
                'professors_count' => 75,
                'departments' => [
                    ['name' => 'Economics and Management (İqtisadiyyat və Menecment Departamenti)', 'code' => 'ECON-DEPT', 'slug' => 'econ-dept', 'head' => 'Dr. Ceyhun Məmmədov'],
                ],
            ],
            [
                'name' => 'School of Humanities, Education and Social Sciences (Humanitar, Təhsil və Sosial Elmlər Məktəbi)',
                'slug' => 'school-of-humanities-education-and-social-sciences',
                'code' => 'HUM-EDU',
                'dean_name' => 'Dos. Elza Səmədova',
                'description' => 'Beynəlxalq münasibətlər, politologiya, psixologiya, ingilis dili və təhsilin idarə edilməsi sahəsində aparıcı təhsil mərkəzi.',
                'icon' => 'globe',
                'color' => 'cyan',
                'established_year' => 1992,
                'students_count' => 1400,
                'professors_count' => 85,
                'departments' => [
                    ['name' => 'English Language and Literature (İngilis Dili və Ədəbiyyatı)', 'code' => 'ENG-LANG-DEPT', 'slug' => 'eng-lang-dept', 'head' => 'Dr. Bahman Amani'],
                    ['name' => 'Political Science and Philosophy (Politologiya və Fəlsəfə)', 'code' => 'POL-DEPT', 'slug' => 'pol-dept', 'head' => 'Dr. Eldar Şahgəldiyev'],
                    ['name' => 'Psychology (Psixologiya Departamenti)', 'code' => 'PSY-DEPT', 'slug' => 'psy-dept', 'head' => 'Dos. Melek Kərimova'],
                    ['name' => 'History and Archaeology (Tarix və Arxeologiya)', 'code' => 'HIST-DEPT', 'slug' => 'hist-dept', 'head' => 'Dr. Telman Nüsrətoğlu'],
                    ['name' => 'Education (Təhsil Elmləri Departamenti)', 'code' => 'EDU-DEPT', 'slug' => 'edu-dept', 'head' => 'Dos. Elza Səmədova'],
                ],
            ],
            [
                'name' => 'Hasvet School of Veterinary Medicine (Hasvet Baytarlıq Təbabəti Məktəbi)',
                'slug' => 'hasvet-school-of-veterinary-medicine',
                'code' => 'VET-MED',
                'dean_name' => 'Prof. Dr. Hidayət Məmmədov',
                'description' => 'Müasir baytarlıq təbabəti, cərrahiyyə və heyvan sağlamlığı elmləri üzrə beynəlxalq klinik tədris mərkəzi.',
                'icon' => 'stethoscope',
                'color' => 'emerald',
                'established_year' => 2020,
                'students_count' => 380,
                'professors_count' => 30,
                'departments' => [
                    ['name' => 'Veterinary Medicine (Baytarlıq Təbabəti Departamenti)', 'code' => 'VET-DEPT', 'slug' => 'vet-dept', 'head' => 'Prof. Hidayət Məmmədov'],
                ],
            ],
        ];

        $allCreatedSchools = [];
        $allCreatedDepartments = [];

        foreach ($schoolsData as $sData) {
            $depts = $sData['departments'];
            unset($sData['departments']);
            $school = Faculty::create($sData);
            $allCreatedSchools[] = $school;

            foreach ($depts as $dData) {
                $dept = Department::create([
                    'faculty_id' => $school->id,
                    'name' => $dData['name'],
                    'code' => $dData['code'],
                    'slug' => $dData['slug'],
                    'head_name' => $dData['head'],
                    'description' => "Xəzər Universiteti {$dData['name']} üzrə tədris və elmi tədqiqat bölməsi.",
                ]);
                $allCreatedDepartments[$dData['code']] = $dept;
            }
        }

        // 3. Official Khazar Programs (BSc, MSc, PhD)
        $csDept = $allCreatedDepartments['CS-DEPT'];
        $bioDept = $allCreatedDepartments['BIO-DEPT'];
        $petDept = $allCreatedDepartments['PET-DEPT'];
        $econDept = $allCreatedDepartments['ECON-DEPT'];
        $engLangDept = $allCreatedDepartments['ENG-LANG-DEPT'];

        $programsData = [
            [
                'department_id' => $csDept->id,
                'name' => 'Computer Science (Kompüter Elmləri — Bakalavr)',
                'slug' => 'computer-science-bsc',
                'code' => 'CS-BSC',
                'degree_level' => 'bachelor',
                'duration_years' => 4,
                'credits_total' => 240,
                'tuition_fee_azn' => 5000.00,
                'language' => 'İngilis',
                'study_mode' => 'Əyani',
                'overview' => 'Proqram mühəndisliyi, alqoritmlər, süni intellekt və böyük verilənlər bazaları üzrə beynəlxalq standartlı tədris planı.',
                'career_opportunities' => 'Software Engineer, Data Analyst, Cloud Solutions Architect, Full-Stack Developer.',
                'admission_requirements' => 'DİM 1-ci qrup (Minimum 560 bal) və ya SAT (Minimum 1200), İngilis dili minimum B2.',
                'min_score_required' => 560,
                'is_popular' => true,
            ],
            [
                'department_id' => $csDept->id,
                'name' => 'Computer Engineering & Artificial Intelligence (Kompüter Mühəndisliyi — Magistr)',
                'slug' => 'computer-engineering-ai-msc',
                'code' => 'CE-MSC',
                'degree_level' => 'master',
                'duration_years' => 2,
                'credits_total' => 120,
                'tuition_fee_azn' => 4500.00,
                'language' => 'İngilis',
                'study_mode' => 'Əyani',
                'overview' => 'Dərin neyron şəbəkələri, maşın öyrənməsi, paylanmış arxitekturalar və tətbiqi elmi tədqiqat dissertasiyası.',
                'career_opportunities' => 'Lead AI Researcher, Machine Learning Engineer, Systems Architect.',
                'admission_requirements' => 'Kompüter elmləri və ya mühəndislik üzrə bakalavr dərəcəsi, GPA 3.0+ (4.0 şkalası ilə).',
                'min_score_required' => 70,
                'is_popular' => true,
            ],
            [
                'department_id' => $petDept->id,
                'name' => 'Petroleum Engineering (Neft-Qaz Mühəndisliyi — Bakalavr)',
                'slug' => 'petroleum-engineering-bsc',
                'code' => 'PET-BSC',
                'degree_level' => 'bachelor',
                'duration_years' => 4,
                'credits_total' => 240,
                'tuition_fee_azn' => 4800.00,
                'language' => 'İngilis',
                'study_mode' => 'Əyani',
                'overview' => 'Lay mühəndisliyi, qazma texnologiyaları və quyuların istismarı sahəsində BP və SOCAR ilə sıx əməkdaşlıq proqramı.',
                'career_opportunities' => 'Reservoir Engineer, Drilling Specialist, Production Engineer.',
                'admission_requirements' => 'DİM 1-ci qrup (Minimum 500 bal) və ya SAT.',
                'min_score_required' => 500,
                'is_popular' => true,
            ],
            [
                'department_id' => $econDept->id,
                'name' => 'Business Administration & Finance (Biznes İdarəçiliyi və Maliyyə — Bakalavr)',
                'slug' => 'business-administration-finance-bba',
                'code' => 'BBA-FIN',
                'degree_level' => 'bachelor',
                'duration_years' => 4,
                'credits_total' => 240,
                'tuition_fee_azn' => 4500.00,
                'language' => 'İngilis',
                'study_mode' => 'Əyani',
                'overview' => 'Beynəlxalq maliyyə idarəçiliyi, investisiya təhlili, korporativ idarəetmə və qlobal audit.',
                'career_opportunities' => 'Financial Analyst, Investment Banker, Audit Associate, Operations Manager.',
                'admission_requirements' => 'DİM 2-ci qrup (Minimum 520 bal) və ya SAT.',
                'min_score_required' => 520,
                'is_popular' => true,
            ],
            [
                'department_id' => $bioDept->id,
                'name' => 'Molecular Biology & Genetics (Molekulyar Biologiya — Bakalavr)',
                'slug' => 'molecular-biology-genetics-bsc',
                'code' => 'BIO-BSC',
                'degree_level' => 'bachelor',
                'duration_years' => 4,
                'credits_total' => 240,
                'tuition_fee_azn' => 4000.00,
                'language' => 'İngilis',
                'study_mode' => 'Əyani',
                'overview' => 'Genomik analiz, hüceyrə biologiyası, biotexnologiya və müasir tibbi laboratoriya tədqiqatları.',
                'career_opportunities' => 'Genetics Specialist, Biomedical Researcher, Clinical Laboratory Scientist.',
                'admission_requirements' => 'DİM 4-cü qrup (Minimum 500 bal).',
                'min_score_required' => 500,
                'is_popular' => false,
            ],
            [
                'department_id' => $engLangDept->id,
                'name' => 'English Language and Translation (İngilis Dili və Tərcümə — Bakalavr)',
                'slug' => 'english-language-translation-ba',
                'code' => 'ENG-TRANS',
                'degree_level' => 'bachelor',
                'duration_years' => 4,
                'credits_total' => 240,
                'tuition_fee_azn' => 4200.00,
                'language' => 'İngilis / Azərbaycan',
                'study_mode' => 'Əyani',
                'overview' => 'Sinxron tərcümə, akademik yazı, beynəlxalq diplomatiya dili və dilçilik nəzəriyyəsi.',
                'career_opportunities' => 'Simultaneous Interpreter, International Relations Coordinator, Copywriter.',
                'admission_requirements' => 'DİM 3-cü qrup (Minimum 530 bal).',
                'min_score_required' => 530,
                'is_popular' => true,
            ],
        ];

        $createdPrograms = [];
        foreach ($programsData as $pData) {
            $createdPrograms[] = Program::create($pData);
        }

        // 4. Official Khazar Users with @khazar.org Emails
        // System Admin
        $systemAdmin = new User([
            'name' => 'Rektorluq Administratoru',
            'email' => 'admin@khazar.org',
            'password' => Hash::make('password'),
            'role_title' => 'Baş Sistem Administratoru (Super Admin)',
            'staff_id' => 'KU-SYS-001',
            'phone' => '+994 12 421 10 93',
            'bio' => 'Xəzər Universitetinin rəsmi informasiya texnologiyaları və akademik reyestr rəhbəri.',
            'status' => 'active',
        ]);
        $systemAdmin->role = 'system_admin';
        $systemAdmin->save();
        $systemAdmin->roles()->attach($roleSystemAdmin);

        // Academic Admin / Dean
        $deanEng = new User([
            'name' => 'Prof. Dr. Fəxrəddin Məmmədov',
            'email' => 'dean.eng@khazar.org',
            'password' => Hash::make('password'),
            'role_title' => 'Mühəndislik Məktəbi Dekanı',
            'staff_id' => 'KU-ADM-012',
            'faculty_id' => $allCreatedSchools[0]->id,
            'department_id' => $csDept->id,
            'phone' => '+994 12 421 79 16',
            'bio' => 'Xəzər Universiteti Təbiət Elmləri və Mühəndislik Məktəbinin dekanı, IEEE üzvü.',
            'status' => 'active',
        ]);
        $deanEng->role = 'admin';
        $deanEng->save();
        $deanEng->roles()->attach($roleAdmin);

        // Professor / Teacher
        $teacherFakhraddin = new User([
            'name' => 'Prof. Dr. Fəxrəddin Məmmədov',
            'email' => 'prof.fakhraddin@khazar.org',
            'password' => Hash::make('password'),
            'role_title' => 'Professor / Kompüter Elmləri Kafedra Müdiri',
            'staff_id' => 'KU-PRF-104',
            'faculty_id' => $allCreatedSchools[0]->id,
            'department_id' => $csDept->id,
            'phone' => '+994 12 421 79 16',
            'bio' => 'Alqoritmlər, paylanmış sistemlər və süni intellekt üzrə 70-dən çox beynəlxalq elmi məqalə müəllifi.',
            'status' => 'active',
        ]);
        $teacherFakhraddin->role = 'teacher';
        $teacherFakhraddin->save();
        $teacherFakhraddin->roles()->attach($roleTeacher);

        $teacherIrada = new User([
            'name' => 'Dos. Dr. İradə Xəlilova',
            'email' => 'prof.irada@khazar.org',
            'password' => Hash::make('password'),
            'role_title' => 'Dosent / Həyat Elmləri Departament Rəhbəri',
            'staff_id' => 'KU-PRF-208',
            'faculty_id' => $allCreatedSchools[0]->id,
            'department_id' => $bioDept->id,
            'phone' => '+994 12 421 79 27',
            'bio' => 'Molekulyar biologiya, hüceyrə genetikası və biotexnologiya üzrə tədqiqatçı alim.',
            'status' => 'active',
        ]);
        $teacherIrada->role = 'teacher';
        $teacherIrada->save();
        $teacherIrada->roles()->attach($roleTeacher);

        // Bachelor Student
        $studentMurad = new User([
            'name' => 'Murad Hüseynov',
            'email' => 'student.murad@khazar.org',
            'password' => Hash::make('password'),
            'role_title' => 'Bakalavr Tələbəsi (III Kurs)',
            'student_id' => 'KU-2023-4102',
            'faculty_id' => $allCreatedSchools[0]->id,
            'department_id' => $csDept->id,
            'degree_level' => 'bachelor',
            'admission_year' => 2023,
            'phone' => '+994 55 777 88 99',
            'bio' => 'Kompüter elmləri ixtisası üzrə fəal tələbə, proqramlaşdırma olimpiadalarının iştirakçısı.',
            'status' => 'active',
        ]);
        $studentMurad->role = 'student';
        $studentMurad->save();
        $studentMurad->roles()->attach($roleStudent);

        // Master Student
        $masterAygun = new User([
            'name' => 'Aygün Rzayeva',
            'email' => 'master.aygun@khazar.org',
            'password' => Hash::make('password'),
            'role_title' => 'Magistrant & Tədqiqatçı (II Kurs)',
            'student_id' => 'KU-2024-8021',
            'faculty_id' => $allCreatedSchools[0]->id,
            'department_id' => $csDept->id,
            'degree_level' => 'master',
            'admission_year' => 2024,
            'phone' => '+994 70 888 99 00',
            'bio' => 'Kompüter mühəndisliyi üzrə magistr dissertantı, neyron şəbəkələri və NLP tədqiqatçısı.',
            'status' => 'active',
        ]);
        $masterAygun->role = 'master_student';
        $masterAygun->save();
        $masterAygun->roles()->attach($roleMasterStudent);

        // Applicant
        $applicantKenan = new User([
            'name' => 'Kənan Qasımov',
            'email' => 'applicant.kenan@gmail.com',
            'password' => Hash::make('password'),
            'role_title' => 'Qəbul Namizədi / Abituriyent',
            'phone' => '+994 51 999 00 11',
            'bio' => '2026/2027 tədris ili üçün Kompüter Elmləri ixtisasına müraciət etmiş namizəd.',
            'status' => 'pending',
        ]);
        $applicantKenan->role = 'applicant';
        $applicantKenan->save();
        $applicantKenan->roles()->attach($roleApplicant);

        // 5. Khazar Courses & Sections
        $csProg = $createdPrograms[0]; // CS BSc
        $coursesData = [
            ['code' => 'CS101', 'name' => 'Data Structures and Algorithms (Alqoritmlər və Məlumat Strukturları)', 'credits' => 6, 'semester' => 1, 'type' => 'compulsory', 'syllabus' => 'Massivlər, əlaqəli siyahılar, ikili axtarış ağacları, qraflar və O(N) zaman mürəkkəbliyi analizi.'],
            ['code' => 'CS202', 'name' => 'Object-Oriented Programming & Design Patterns (Obyektyönlü Proqramlaşdırma)', 'credits' => 6, 'semester' => 2, 'type' => 'compulsory', 'syllabus' => 'İnkapulyasiya, polimorfizm, SOLID prinsipləri və müasir C++ / Java dizayn patternləri.'],
            ['code' => 'AI301', 'name' => 'Machine Learning and Neural Networks (Süni İntellekt və Neyron Şəbəkələri)', 'credits' => 6, 'semester' => 5, 'type' => 'compulsory', 'syllabus' => 'Xətti və loqistik reqressiya, qradiyent enməsi, CNN, RNN və PyTorch tətbiqləri.'],
            ['code' => 'MATH201', 'name' => 'Linear Algebra and Probability (Xətti Cəbr və Ehtimal Nəzəriyyəsi)', 'credits' => 6, 'semester' => 3, 'type' => 'compulsory', 'syllabus' => 'Vektor fəzaları, matris çevrilmələri, reqressiya analizi və ehtimal paylanmaları.'],
            ['code' => 'CS304', 'name' => 'Distributed Systems and Cloud Computing (Paylanmış Sistemlər və Bulud)', 'credits' => 6, 'semester' => 6, 'type' => 'elective', 'syllabus' => 'gRPC, Docker konteynerləri, Kubernetes və mikroservis arxitekturaları.'],
        ];

        $createdCourses = [];
        foreach ($coursesData as $c) {
            $createdCourses[] = Course::create(array_merge($c, ['program_id' => $csProg->id]));
        }

        // Sections
        $sec1 = CourseSection::create([
            'course_id' => $createdCourses[0]->id, // CS101
            'teacher_id' => $teacherFakhraddin->id,
            'semester_term' => '2026-Bahar',
            'room_number' => 'Neftçilər Korpusu — Lab 402',
            'day_of_week' => 'Bazar ertəsi',
            'start_time' => '10:00',
            'end_time' => '11:30',
            'capacity' => 35,
        ]);

        $sec2 = CourseSection::create([
            'course_id' => $createdCourses[2]->id, // AI301
            'teacher_id' => $teacherFakhraddin->id,
            'semester_term' => '2026-Bahar',
            'room_number' => 'Neftçilər Korpusu — Aud. 312',
            'day_of_week' => 'Çərşənbə',
            'start_time' => '14:00',
            'end_time' => '15:30',
            'capacity' => 30,
        ]);

        // Enrollments for Murad (Student)
        $enr1 = new Enrollment([
            'student_id' => $studentMurad->id,
            'course_section_id' => $sec1->id,
            'midterm_score' => 46.00,
            'final_score' => 48.00,
            'attendance_percentage' => 96.00,
            'status' => 'completed',
        ]);
        $enr1->total_score = 94.00;
        $enr1->letter_grade = 'A';
        $enr1->save();

        $enr2 = new Enrollment([
            'student_id' => $studentMurad->id,
            'course_section_id' => $sec2->id,
            'midterm_score' => 43.00,
            'final_score' => 42.00,
            'attendance_percentage' => 92.00,
            'status' => 'completed',
        ]);
        $enr2->total_score = 85.00;
        $enr2->letter_grade = 'B';
        $enr2->save();

        // Trigger automatic GPA recalculation
        $gradingService = app(GradingService::class);
        $gradingService->recalculateStudentGpa($studentMurad);

        // Enrollments for Aygun (Master)
        $enr3 = new Enrollment([
            'student_id' => $masterAygun->id,
            'course_section_id' => $sec2->id,
            'midterm_score' => 48.00,
            'final_score' => 47.50,
            'attendance_percentage' => 98.00,
            'status' => 'completed',
        ]);
        $enr3->total_score = 95.50;
        $enr3->letter_grade = 'A';
        $enr3->save();
        $gradingService->recalculateStudentGpa($masterAygun);

        // 6. Master Thesis for Aygun
        StudentThesis::create([
            'student_id' => $masterAygun->id,
            'advisor_id' => $teacherFakhraddin->id,
            'title' => 'Optimization of Transformer Models for Low-Resource Agglutinative Languages',
            'research_field' => 'Natural Language Processing & Deep Learning',
            'abstract' => 'Xəzər Universitetinin Kompüter Elmləri laboratoriyasında aparılan bu tədqiqat işi Azərbaycan dili kimi aqlütinativ dillər üçün transformator modellərinin parametr optimizasiyasını araşdırır.',
            'stage' => 'drafting',
            'progress_percentage' => 80,
            'expected_defense_date' => '2026-06-20',
        ]);

        // 1. Academic Terms
        $termFall2025 = AcademicTerm::create([
            'name' => '2025 Payız Semestri',
            'code' => '2025-Payiz',
            'start_date' => '2025-09-15',
            'end_date' => '2026-01-20',
            'is_current' => false,
            'registration_open' => false,
        ]);

        $termSpring2026 = AcademicTerm::create([
            'name' => '2026 Bahar Semestri',
            'code' => '2026-Bahar',
            'start_date' => '2026-02-15',
            'end_date' => '2026-06-30',
            'is_current' => true,
            'registration_open' => true,
        ]);

        $termSummer2026 = AcademicTerm::create([
            'name' => '2026 Yay Semestri',
            'code' => '2026-Yay',
            'start_date' => '2026-07-05',
            'end_date' => '2026-08-25',
            'is_current' => false,
            'registration_open' => false,
        ]);

        // 2. Admissions Application for Kenan
        $app = AdmissionsApplication::create([
            'application_number' => 'KU-APP-2026-8812',
            'user_id' => $applicantKenan->id,
            'program_id' => $csProg->id,
            'first_name' => 'Kənan',
            'last_name' => 'Qasımov',
            'email' => 'applicant.kenan@gmail.com',
            'phone' => '+994 51 999 00 11',
            'passport_fin' => '7ABC991',
            'birth_date' => '2007-04-15',
            'gender' => 'Kişi',
            'nationality' => 'Azərbaycan',
            'high_school_gpa' => 4.90,
            'exam_score' => 642,
            'exam_type' => 'DİM',
            'dim_work_number' => 'DIM-2026-88210',
            'degree_level' => 'bachelor',
            'status' => 'under_review',
            'reviewer_notes' => 'Yüksək DİM balı və riyaziyyat üzrə olimpiada nailiyyəti mövcuddur. Xəzər Qəbul Komissiyası tərəfindən müsahibəyə tövsiyə edilir.',
        ]);

        ApplicationDocument::create([
            'admissions_application_id' => $app->id,
            'document_type' => 'Attestat və Qiymət Cədvəli',
            'file_path' => 'documents/attestat_kenan.pdf',
            'original_name' => 'Attestat_Qasimov_Kenan.pdf',
            'file_size_kb' => 1420,
        ]);

        // Second DİM Applicant
        AdmissionsApplication::create([
            'application_number' => 'KU-APP-2026-9043',
            'program_id' => $csProg->id,
            'first_name' => 'Nigar',
            'last_name' => 'Əliyeva',
            'email' => 'nigar.dim.candidate@gmail.com',
            'phone' => '+994 50 444 33 22',
            'passport_fin' => '6XYZ781',
            'birth_date' => '2007-08-20',
            'gender' => 'Qadın',
            'nationality' => 'Azərbaycan',
            'high_school_gpa' => 4.95,
            'exam_score' => 675,
            'exam_type' => 'DİM',
            'dim_work_number' => 'DIM-2026-90145',
            'degree_level' => 'bachelor',
            'status' => 'under_review',
            'reviewer_notes' => 'Respublika fənn müsabiqəsi qalibi, 675 DİM balı.',
        ]);

        // Student with Debt for testing debt hold
        $studentDebt = new User([
            'name' => 'Rəşad Əliyev (Borclu Tələbə)',
            'email' => 'student.debt@khazar.org',
            'password' => Hash::make('password'),
            'role_title' => 'Bakalavr Tələbəsi (Borclu)',
            'student_id' => 'KU-2023-9901',
            'faculty_id' => $allCreatedSchools[0]->id,
            'department_id' => $csDept->id,
            'degree_level' => 'bachelor',
            'admission_year' => 2023,
            'phone' => '+994 50 111 22 33',
            'bio' => 'Təhsil haqqı borcu səbəbilə qeydiyyatı bloklanmış test tələbəsi.',
            'status' => 'active',
        ]);
        $studentDebt->role = 'student';
        $studentDebt->save();
        $studentDebt->roles()->attach($roleStudent);

        // Debt Invoice (unpaid & overdue)
        TuitionInvoice::create([
            'student_id' => $studentDebt->id,
            'invoice_number' => 'KU-INV-2025-9999',
            'semester_term' => '2025-Payiz',
            'amount' => 2500.00,
            'due_date' => now()->subDays(30),
            'status' => 'overdue',
        ]);

        // 3. Official Khazar University Campuses & Buildings
        $buildingsData = [
            [
                'name' => 'Neftçilər Korpusu (Əsas Tədris Kampusu)',
                'code' => 'KU-NEFT',
                'category' => 'academic',
                'description' => 'Məhsəti Gəncəvi küç. 41. Rektorluq, Mühəndislik və Təbiət Elmləri Məktəbi, elmi laboratoriyalar və auditoriyalar.',
                'floors_count' => 7,
                'capacity' => 2500,
                'map_x' => 45,
                'map_y' => 40,
                'facilities' => ['Yüksək Sürətli Wi-Fi', 'Kompüter və AI Laboratoriyaları', 'Kafeteriya', 'Tibbi Məntəqə'],
            ],
            [
                'name' => 'Bəşir Səfəroğlu Korpusu (Downtown / Şəhər Kampusu)',
                'code' => 'KU-DOWN',
                'category' => 'academic',
                'description' => 'Bəşir Səfəroğlu küç. 122. İqtisadiyyat və Menecment Məktəbi, MBA proqramları və biznes inkubatoru.',
                'floors_count' => 5,
                'capacity' => 1200,
                'map_x' => 65,
                'map_y' => 55,
                'facilities' => ['Biznes Tədqiqat Mərkəzi', 'Konfrans Zalı', 'Kovorkinq Zonası'],
            ],
            [
                'name' => 'Mərkəzi Kitabxana və İnformasiya Mərkəzi (LIC)',
                'code' => 'KU-LIB',
                'category' => 'library',
                'description' => 'Zəngin beynəlxalq kitab fondu, EBSCO, JSTOR, ScienceDirect elektron bazalarına 24/7 çıxış və sakit oxu zalları.',
                'floors_count' => 3,
                'capacity' => 600,
                'map_x' => 35,
                'map_y' => 60,
                'facilities' => ['24/7 Elektron Arxiv', 'Fərdi Tədqiqat Kabinələri', 'Multimedia Zalı'],
            ],
            [
                'name' => 'Binəqədi Korpusu və Dünya Məktəbi Kompleksi',
                'code' => 'KU-BIN',
                'category' => 'academic',
                'description' => 'Əhməd Rəcəbli küç. Təhsil elmləri, laboratoriyalar və beynəlxalq IB proqramı mərkəzi.',
                'floors_count' => 4,
                'capacity' => 900,
                'map_x' => 75,
                'map_y' => 30,
                'facilities' => ['Qapalı İdman Arenası', 'Musiqi və İncəsənət Studiyası', 'Kimya Laboratoriyası'],
            ],
            [
                'name' => 'Fikrət Əmirov Konfrans və Konsert Zalı',
                'code' => 'KU-AMIR',
                'category' => 'sports',
                'description' => 'Xəzər Universitetinin beynəlxalq simpoziumlar, mədəni tədbirlər və qala konsertləri keçirilən böyük akt zalı.',
                'floors_count' => 2,
                'capacity' => 500,
                'map_x' => 25,
                'map_y' => 30,
                'facilities' => ['Professional Akustika', 'Simxron Tərcümə Kabinələri', 'VİP Qonaq Zalı'],
            ],
        ];

        foreach ($buildingsData as $b) {
            CampusBuilding::create($b);
        }

        // 4. Research Papers
        ResearchPaper::create([
            'title' => 'Mathematical Modeling and AI-Driven Exploration in South Caspian Petroleum Basins',
            'slug' => 'mathematical-modeling-ai-caspian-basins',
            'author_id' => $teacherFakhraddin->id,
            'co_authors' => 'Prof. Hamlet İsaxanlı, Dr. Gəray Qasımov',
            'category' => 'Petroleum Engineering & AI',
            'abstract' => 'This study presents novel algorithmic reservoir simulation models combining seismic inversion data with convolutional neural networks for enhanced hydrocarbon recovery in deep Caspian offshore fields.',
            'journal_name' => 'Khazar Journal of Science and Technology',
            'doi' => '10.5782/2223-2621.2025.28.1.45',
            'publication_date' => '2025-10-15',
            'citations_count' => 34,
            'is_featured' => true,
        ]);

        ResearchPaper::create([
            'title' => 'Cellular and Molecular Biomarkers in Flora of the Caucasus Region',
            'slug' => 'cellular-molecular-biomarkers-caucasus',
            'author_id' => $teacherIrada->id,
            'co_authors' => 'Aygün Rzayeva',
            'category' => 'Life Sciences & Genetics',
            'abstract' => 'Comprehensive genetic sequencing of endemic medicinal plants identifying active bio-compounds with potential antioxidant applications in pharmacology.',
            'journal_name' => 'Khazar Journal of Humanities and Social Sciences',
            'doi' => '10.5782/2223-2621.2026.01.12',
            'publication_date' => '2026-02-10',
            'citations_count' => 18,
            'is_featured' => true,
        ]);

        // 5. Events
        Event::create([
            'title' => 'Beynəlxalq Xəzər Elmi Simpoziumu və İnnovasiya Forumu 2026',
            'slug' => 'international-khazar-scientific-symposium-2026',
            'category' => 'academic',
            'description' => 'Avropa və ABŞ-ın tərəfdaş universitetlərinin alimləri, professor heyəti və doktorantların iştirakı ilə illik elmi simpozium.',
            'speaker_names' => 'Prof. Hamlet İsaxanlı, Prof. Dr. Fəxrəddin Məmmədov, Dəvət olunmuş beynəlxalq alimlər',
            'venue' => 'Fikrət Əmirov Konfrans Zalı (Neftçilər Korpusu)',
            'start_time' => now()->addDays(14)->setTime(10, 0),
            'end_time' => now()->addDays(15)->setTime(17, 0),
            'capacity' => 450,
            'registered_count' => 320,
            'is_featured' => true,
        ]);

        Event::create([
            'title' => 'Xəzər Karyera və Məzun Yarmarkası 2026',
            'slug' => 'khazar-career-fair-2026',
            'category' => 'career',
            'description' => 'Aparıcı banklar, audit şirkətləri (Big 4), İT və mühəndislik korporasiyalarının iştirakı ilə tələbələr üçün birbaşa iş imkanları.',
            'speaker_names' => 'BP, SOCAR, PASHA Bank, Ernst & Young, Azercell nümayəndələri',
            'venue' => 'Neftçilər Korpusu Mərkəzi Foyesi',
            'start_time' => now()->addDays(24)->setTime(11, 0),
            'end_time' => now()->addDays(24)->setTime(16, 30),
            'capacity' => 800,
            'registered_count' => 540,
            'is_featured' => true,
        ]);

        // 6. Authentic News Articles
        NewsArticle::create([
            'title' => 'Xəzər Universiteti Beynəlxalq Reytinqlərdə Qabaqcıl Mövqeyini Qoruyur',
            'slug' => 'khazar-university-global-rankings-success',
            'category' => 'universitet',
            'summary' => 'QS və Times Higher Education reytinq cədvəllərində təhsil keyfiyyəti və elmi nəşrlərin sitat sayına görə ölkəmizin aparıcı ali məktəbi kimi qeyd olunub.',
            'content' => '<p>Xəzər Universiteti 1991-ci ildə təsis edildiyi gündən etibarən beynəlxalq təhsil standartlarını və Qərb kredit sistemini ölkəmizdə tətbiq edən ilk ali məktəbdir.</p><p>Son illərdə həyata keçirilən elmi tədqiqatlar və beynəlxalq indeksli jurnallarda dərc olunan məqalələrin sayı universitetin qlobal nüfuzunu daha da gücləndirmişdir.</p>',
            'views_count' => 3120,
            'is_featured' => true,
            'published_at' => now()->subDays(2),
        ]);

        NewsArticle::create([
            'title' => 'Mühəndislik və Təbiət Elmləri Məktəbində Yeni Biotexnologiya Mərkəzi İstifadəyə Verildi',
            'slug' => 'new-biotechnology-research-center-khazar',
            'category' => 'elm',
            'summary' => 'Molekulyar biologiya və genetika tələbələrinin beynəlxalq qrant layihələri çərçivəsində tədqiqat aparması üçün ən müasir avadanlıqlar quraşdırıldı.',
            'content' => '<p>Yeni mərkəz tələbə və magistrantlara DNT sekvensiyası və hüceyrə səviyyəsində dərin analizlər aparmaq imkanı yaradır.</p>',
            'views_count' => 2100,
            'is_featured' => true,
            'published_at' => now()->subDays(5),
        ]);

        // 7. Tuition Invoices for Murad
        TuitionInvoice::create([
            'student_id' => $studentMurad->id,
            'invoice_number' => 'KU-INV-2026-0042',
            'semester_term' => '2026-Bahar',
            'amount' => 2500.00,
            'due_date' => now()->addDays(20),
            'status' => 'paid',
            'paid_at' => now()->subDays(8),
            'payment_method' => 'Onlayn Bank Kartı',
            'transaction_id' => 'TXN-9843-12847-AZ',
            'receipt_number' => 'KU-REC-2026-0042',
        ]);

        // 8. LMS Features Seed (Assignments, Materials, Announcements, Attendance)
        // Set Prerequisite for CS202 -> CS101
        $createdCourses[1]->prerequisite_course_id = $createdCourses[0]->id;
        $createdCourses[1]->save();

        // Additional Sections for Registration Testing
        CourseSection::create([
            'course_id' => $createdCourses[1]->id, // CS202
            'teacher_id' => $teacherFakhraddin->id,
            'semester_term' => '2026-Bahar',
            'room_number' => 'Neftçilər Korpusu — Lab 304',
            'day_of_week' => 'Çərşənbə axşamı',
            'start_time' => '11:00',
            'end_time' => '12:30',
            'capacity' => 25,
        ]);

        CourseSection::create([
            'course_id' => $createdCourses[3]->id, // MATH201
            'teacher_id' => $teacherFakhraddin->id,
            'semester_term' => '2026-Bahar',
            'room_number' => 'Neftçilər Korpusu — Aud. 201',
            'day_of_week' => 'Cümə axşamı',
            'start_time' => '09:00',
            'end_time' => '10:30',
            'capacity' => 40,
        ]);

        // LMS Content for Section 1 (CS101)
        $assign1 = Assignment::create([
            'course_section_id' => $sec1->id,
            'title' => 'Laboratoriya İşi 1: İkili Axtarış Ağaclarının Balanslaşdırılması',
            'description' => 'AVL və Red-Black ağaclarının Java və ya C++ dilində reallaşdırılması və O(log N) axtarış müddətinin benchmark analizi.',
            'max_score' => 10.00,
            'due_date' => now()->addDays(7),
        ]);

        AssignmentSubmission::create([
            'assignment_id' => $assign1->id,
            'student_id' => $studentMurad->id,
            'submission_text' => 'Hörmətli professor, AVL ağacı və rotasiya testləri əlavə olunmuşdur.',
            'file_path' => 'submissions/murad_avl_tree.zip',
            'score' => 9.50,
            'feedback' => 'Əla işdir. Sol və sağ rotasiyalar düzgün həyata keçirilib.',
            'submitted_at' => now()->subDays(2),
        ]);

        CourseMaterial::create([
            'course_section_id' => $sec1->id,
            'title' => 'Mühazirə 1: Asimptotik Analiz və Big-O Notasiyası',
            'type' => 'file',
            'file_path_or_url' => 'materials/lecture1_big_o.pdf',
            'is_published' => true,
        ]);

        CourseAnnouncement::create([
            'course_section_id' => $sec1->id,
            'title' => 'Kollokvium Tarixi və İmtahan Qaydaları',
            'content' => 'Hörmətli tələbələr, 1-ci aralıq kollokvium növbəti həftənin çərşənbə günü Lab 402 auditoriyasında keçiriləcəkdir.',
            'published_at' => now()->subDays(3),
        ]);

        // Per-Session Attendance Records for Murad
        for ($i = 1; $i <= 5; $i++) {
            AttendanceRecord::create([
                'enrollment_id' => $enr1->id,
                'session_date' => now()->subDays(6 - $i)->toDateString(),
                'status' => $i == 4 ? 'late' : 'present',
                'notes' => $i == 4 ? '5 dəqiqə gecikdi' : 'Dərsdə iştirak etdi',
            ]);
        }

        // 9. Grade Distribution & Score Breakdown Seed
        $comp1 = GradeComponent::create([
            'course_section_id' => $sec1->id,
            'name' => 'Davamiyyət və Dərsdə Fəallıq',
            'max_score' => 10.00,
            'type' => 'attendance',
            'order_index' => 1,
        ]);

        $comp2 = GradeComponent::create([
            'course_section_id' => $sec1->id,
            'name' => 'Seminar və Məşğələ Dərsləri',
            'max_score' => 10.00,
            'type' => 'seminar',
            'order_index' => 2,
        ]);

        $comp3 = GradeComponent::create([
            'course_section_id' => $sec1->id,
            'name' => 'Laboratoriya və Sərbəst İşlər',
            'max_score' => 10.00,
            'type' => 'assignment',
            'order_index' => 3,
        ]);

        $comp4 = GradeComponent::create([
            'course_section_id' => $sec1->id,
            'name' => 'Kollokvium / Aralıq İmtahan',
            'max_score' => 20.00,
            'type' => 'midterm',
            'order_index' => 4,
        ]);

        $comp5 = GradeComponent::create([
            'course_section_id' => $sec1->id,
            'name' => 'Final İmtahanı',
            'max_score' => 50.00,
            'type' => 'final',
            'order_index' => 5,
        ]);

        // Student Component Scores for Murad in CS101
        StudentGradeItem::create([
            'enrollment_id' => $enr1->id,
            'grade_component_id' => $comp1->id,
            'score' => 9.50,
        ]);
        StudentGradeItem::create([
            'enrollment_id' => $enr1->id,
            'grade_component_id' => $comp2->id,
            'score' => 9.00,
        ]);
        StudentGradeItem::create([
            'enrollment_id' => $enr1->id,
            'grade_component_id' => $comp3->id,
            'score' => 9.50,
        ]);
        StudentGradeItem::create([
            'enrollment_id' => $enr1->id,
            'grade_component_id' => $comp4->id,
            'score' => 18.50,
        ]);
        StudentGradeItem::create([
            'enrollment_id' => $enr1->id,
            'grade_component_id' => $comp5->id,
            'score' => 45.00,
        ]);

        // Recalculate Murad's total from components
        $enr1->recalculateTotalFromComponents();

        // Sample Student Appeal for Murad
        StudentAppeal::create([
            'student_id' => $studentMurad->id,
            'course_section_id' => $sec1->id,
            'enrollment_id' => $enr1->id,
            'type' => 'grade_objection',
            'subject' => 'Final İmtahanı 3-cü sualının qiymətləndirilməsinə etiraz',
            'reason' => 'Hörmətli komissiya, 3-cü sualda dinamik proqramlaşdırma üzrə memoization yanaşması tam düzgün izah olunsa da, 2 bal kəsilmişdir. Xahiş edirəm təkrar baxılsın.',
            'status' => 'approved',
            'reviewed_by' => $teacherFakhraddin->id,
            'reviewer_notes' => 'Yazı işi komissiya tərəfindən təkrar yoxlanıldı, texniki nüans nəzərə alınaraq bal 45-ə qaldırıldı.',
            'submitted_at' => now()->subDays(2),
            'responded_at' => now()->subDay(),
        ]);

        // 10. System Notifications
        SystemNotification::create([
            'user_id' => $studentMurad->id,
            'title' => '2026 Bahar Semestri Fənn Qeydiyyatı Başladı',
            'message' => 'Fənn seçimi portalı aktivdir. Zəhmət olmasa tədris planınıza uyğun fənləri seçin.',
            'type' => 'info',
            'link' => route('portal.registration.index'),
            'is_read' => false,
        ]);

        SystemNotification::create([
            'user_id' => $studentMurad->id,
            'title' => 'Təhsil Haqqı Ödənişi Təsdiqləndi',
            'message' => 'KU-INV-2026-0042 nömrəli faktura üzrə 2500 AZN ödənişiniz uğurla qəbul edildi.',
            'type' => 'success',
            'link' => route('portal.finance.receipt', 1),
            'is_read' => true,
        ]);

        // 11. Audit Log Initial Seed
        ActivityLog::create([
            'user_id' => $systemAdmin->id,
            'action' => 'system.initialized',
            'subject_type' => User::class,
            'subject_id' => $systemAdmin->id,
            'description' => 'Xəzər Universiteti Rəqəmsal İdarəetmə Sistemi uğurla başladıldı və ilkin konfiqurasiya tamamlandı.',
            'ip_address' => '127.0.0.1',
        ]);
    }
}
