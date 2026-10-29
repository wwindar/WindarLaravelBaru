<?php

namespace Database\Seeders;

use App\Enums\StatusKarya;
use App\Models\Agenda;
use App\Models\Artikel;
use App\Models\Karya;
use App\Models\Kategori;
use App\Models\RiwayatKarya;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class AksaraKampusSeeder extends Seeder
{
    /**
     * Run the database seeds for Aksara Kampus.
     */
    public function run(): void
    {
        // -------------------------------------------------------------
        // 1. Kategori (6 Kategori)
        // -------------------------------------------------------------
        $kategoriList = [
            ['nama' => 'Puisi', 'slug' => 'puisi'],
            ['nama' => 'Prosa', 'slug' => 'prosa'],
            ['nama' => 'Esai & Kritik', 'slug' => 'esai-kritik'],
            ['nama' => 'Resensi', 'slug' => 'resensi'],
            ['nama' => 'Kepenulisan', 'slug' => 'kepenulisan'],
            ['nama' => 'Kabar Komunitas', 'slug' => 'kabar-komunitas'],
        ];

        $kategoriMap = [];
        foreach ($kategoriList as $item) {
            $kat = Kategori::updateOrCreate(['slug' => $item['slug']], $item);
            $kategoriMap[$item['slug']] = $kat->id;
        }

        // -------------------------------------------------------------
        // 2. Artikel (10 Artikel: 5 Berita, 5 Blog, 3-5 Paragraf Original)
        // -------------------------------------------------------------
        $artikelList = [
            // Berita 1
            [
                'judul' => 'Malam Penganugerahan Sastra Mahasiswa 2026: Merayakan Gagasan di Balik Kata',
                'slug' => 'malam-penganugerahan-sastra-mahasiswa-2026',
                'ringkasan' => 'Aksara Kampus menggelar malam penganugerahan karya sastra terbaik mahasiswa semester ini di Gedung Pertemuan Kebudayaan.',
                'isi' => "Malam penganugerahan sastra mahasiswa tahun ini berlangsung meriah di Gedung Pertemuan Kebudayaan pada Sabtu malam. Ratusan mahasiswa dari berbagai program studi berkumpul untuk menyaksikan apresiasi naskah puisi, cerpen, dan esai pilihan redaksi.\n\nKetua Komunitas Aksara Kampus dalam pidato sambutannya menegaskan bahwa sastra di lingkungan universitas bukan sekadar pelarian estetis, melainkan medium bernalar kritis untuk merefleksikan persoalan kemanusiaan dan dinamika sosial kampus.\n\nAcara ditutup dengan pembacaan kutipan karya peraih penghargaan terbaik tahun ini, diiringi alunan petikan gitar akustik yang syahdu dan tepuk tangan hangat seluruh penonton yang memadati ruangan hingga larut malam.",
                'jenis' => 'berita',
                'kategori_id' => $kategoriMap['kabar-komunitas'],
                'penulis' => 'Redaksi Aksara',
                'status' => 'terbit',
                'diterbitkan_pada' => Carbon::now()->subDays(10),
            ],
            // Berita 2
            [
                'judul' => 'Laboratorium Sastra Kampus Terbitkan Antologi Puisi Lintas Fakultas',
                'slug' => 'laboratorium-sastra-terbitkan-antologi-puisi-lintas-fakultas',
                'ringkasan' => 'Koleksi karya puisi mahasiswa bertajuk Bunga Rampai Lorong Kampus resmi diluncurkan secara cetak dan digital.',
                'isi' => "Komunitas sastra kampus kembali mencetak prestasi literasi dengan meluncurkan buku antologi puisi kolaboratif berjudul 'Bunga Rampai Lorong Kampus'. Proyek penerbitan independen ini menghimpun 40 puisi terbaik karya mahasiswa dari lintas fakultas, mulai dari teknik, hukum, hingga ilmu kesehatan.\n\nKoordinator penerbitan mengungkapkan bahwa proses seleksi naskah memakan waktu hampir tiga bulan penuh. Tim kurator membaca lebih dari seratus naskah kiriman mahasiswa sebelum menetapkan karya-karya yang dinilai kuat dalam karakter imaji dan keutuhan diksi.\n\nPeluncuran buku antologi ini disambut antusias oleh sivitas akademika. Edisi digital buku kini telah dapat diunduh gratis melalui portal resmi, sementara edisi cetak terbatas disalurkan ke perpustakaan pusat dan ruang baca fakultas.",
                'jenis' => 'berita',
                'kategori_id' => $kategoriMap['kabar-komunitas'],
                'penulis' => 'Dian Permadi',
                'status' => 'terbit',
                'diterbitkan_pada' => Carbon::now()->subDays(8),
            ],
            // Berita 3
            [
                'judul' => 'Kunjungan Budaya ke Rumah Puisi: Merawat Ingatan Lewat Narasi',
                'slug' => 'kunjungan-budaya-ke-rumah-puisi-merawat-ingatan-lewat-narasi',
                'ringkasan' => 'Rombongan penulis muda kampus menimba ilmu langsung dari arsip sastra klasik dalam studi lapang ke Rumah Puisi.',
                'isi' => "Sebanyak tiga puluh anggota aktif komunitas literasi kampus mengadakan lawatan ke Rumah Puisi akhir pekan lalu. Kegiatan studi lapang ini bertujuan memperkenalkan jejak sejarah sastra nasional serta manuskrip cetak masa lampau kepada generasi penulis muda.\n\nPara peserta berkesempatan melihat langsung lembaran ketikan asli naskah karya sastrawan terkemuka zaman perintis, lengkap dengan coretan revisi tinta pena. Pengalaman ini menyadarkan mahasiswa bahwa karya besar selalu lahir dari ketekunan menyunting dan ketajaman mengamati kehidupan.\n\nSesi kunjungan ditutup dengan diskusi interaktif bersama kurator museum mengenai bagaimana penulis muda era modern dapat mendokumentasikan keresahan zamannya ke dalam bentuk karya fiksi maupun nonfiksi yang berdaya tahan lama.",
                'jenis' => 'berita',
                'kategori_id' => $kategoriMap['kabar-komunitas'],
                'penulis' => 'Arif Budiman',
                'status' => 'terbit',
                'diterbitkan_pada' => Carbon::now()->subDays(6),
            ],
            // Berita 4
            [
                'judul' => 'Sayembara Penulisan Cerpen Mahasiswa Resmi Dibuka Pekan Ini',
                'slug' => 'sayembara-penulisan-cerpen-mahasiswa-resmi-dibuka',
                'ringkasan' => 'Redaksi mengundang seluruh penulis mahasiswa untuk berpartisipasi dalam ajang sayembara cerpen bertema Jejak Kemanusiaan.',
                'isi' => "Redaksi Aksara Kampus resmi membuka pendaftaran Sayembara Penulisan Cerpen Mahasiswa 2026. Kompetisi tahunan ini menantang para pengarang muda untuk mengeksplorasi tema besar 'Jejak Kemanusiaan' melalui sudut pandang cerita yang segar dan orisinal.\n\nDewan juri sayembara terdiri atas sastrawan tamu dan dosen pembina literasi yang akan menilai naskah berdasarkan kebaruan tema, kekuatan karakterisasi, keluwesan alur, serta ketepatan struktur naratif tanpa meninggalkan unsur kejujuran rasa.\n\nPengiriman naskah berlangsung secara terbuka melalui portal daring ini hingga akhir bulan mendatang. Tiga naskah pemenang berhak mendapatkan penghargaan piala rektorat, uang pembinaan literasi, serta penerbitan khusus di kolom utama.",
                'jenis' => 'berita',
                'kategori_id' => $kategoriMap['prosa'],
                'penulis' => 'Redaksi Aksara',
                'status' => 'terbit',
                'diterbitkan_pada' => Carbon::now()->subDays(4),
            ],
            // Berita 5
            [
                'judul' => 'Diskusi Terbuka Sastra dan Ekologi Soroti Peran Pena Melawan Perubahan Iklim',
                'slug' => 'diskusi-terbuka-sastra-dan-ekologi-soroti-peran-pena',
                'ringkasan' => 'Ekokritik menjadi bahasan hangat dalam ruang dialog sastra bulanan yang diprakarsai oleh mahasiswa pecinta lingkungan dan sastrawan muda.',
                'isi' => "Krisis lingkungan hidup tidak hanya menjadi kajian ilmu sains, tetapi juga panggilan batin bagi para penulis sastra. Hal tersebut mengemuka dalam diskusi panel bertajuk 'Ekokritik: Menulis Bumi di Tengah Krisis' yang diselenggarakan di ruang seminar lantai tiga kemarin siang.\n\nNarasumber memaparkan bahwa teks sastra memiliki daya gugah emosional yang ampuh untuk membangun kesadaran kolektif. Puisi dan cerita pendek mampu menjembatani data statistik iklim yang kaku menjadi kepedulian nyata terhadap pohon, sungai, dan ekosistem yang terancam punah.\n\nDiskusi yang dihadiri puluhan peserta tersebut menghasilkan konsensus untuk memperbanyak terbitan karya-karya sastra yang berorientasi pada konservasi alam dan kelestarian lingkungan hidup di media kampus.",
                'jenis' => 'berita',
                'kategori_id' => $kategoriMap['esai-kritik'],
                'penulis' => 'Sari Handayani',
                'status' => 'terbit',
                'diterbitkan_pada' => Carbon::now()->subDays(2),
            ],

            // Blog 1
            [
                'judul' => 'Mengapa Menulis Catatan Harian Membantu Menemukan Suara Kepenulisanmu',
                'slug' => 'mengapa-menulis-catatan-harian-membantu-menemukan-suara',
                'ringkasan' => 'Buku harian adalah laboratorium pribadi tempat kata-kata diuji tanpa takut dihakimi oleh pembaca lain.',
                'isi' => "Banyak penulis pemula kerap bertanya tentang bagaimana cara menemukan suara khas mereka dalam menulis. Sering kali kita merasa kalimat yang kita tulis terdengar kaku, meniru gaya pengarang tenar, atau terlampau dipaksakan untuk tampak cerdas.\n\nSalah satu cara paling jujur dan ampuh untuk mengikis kepalsuan bahasa itu adalah dengan membiasakan menulis catatan harian. Dalam buku harian pribadi, tidak ada tuntutan estetika, tidak ada kurator redaksi, dan tidak ada ancaman kritik pedas dari pembaca luar.\n\nKetika kita menulis dengan bebas tentang aroma kopi yang dingin, gemerisik hujan di atap genting, atau kegelisahan menjelang ujian akhir, di sanalah kejujuran aksara kita mulai terbentuk dengan alami dan murni.",
                'jenis' => 'blog',
                'kategori_id' => $kategoriMap['kepenulisan'],
                'penulis' => 'Fajar Nugroho',
                'status' => 'terbit',
                'diterbitkan_pada' => Carbon::now()->subDays(9),
            ],
            // Blog 2
            [
                'judul' => 'Secangkir Kopi Pagi dan Menatap Halaman Kosong Buku Catatan',
                'slug' => 'secangkir-kopi-pagi-dan-menatap-halaman-kosong',
                'ringkasan' => 'Refleksi sunyi tentang ketakutan menghadapi lembaran putih dan keberanian menorehkan kata pertama.',
                'isi' => "Ada keheningan yang menakutkan sekaligus memikat ketika kita membuka halaman buku bergaris yang masih bersih tak bernoda di awal hari. Kursor yang berkedip di layar laptop atau ujung pena yang mengambang di atas kertas sering kali membuat kita mematung.\n\nKetakutan terbesar seorang penulis bukanlah kekurangan kosa kata, melainkan ketakutan bahwa apa yang hendak ia ungkapkan tidak cukup berharga untuk dibaca orang lain. Namun, menunggu ilham sempurna jatuh dari langit hanyalah penundaan yang menipu akal sehat.\n\nTulislah kalimat pertamamu meskipun terasa buruk dan canggung. Karena naskah yang buruk selalu bisa diperbaiki melalui penyuntingan, sedangkan halaman yang tetap kosong tak akan pernah mewariskan cerita apa pun.",
                'jenis' => 'blog',
                'kategori_id' => $kategoriMap['kepenulisan'],
                'penulis' => 'Maya Anggraeni',
                'status' => 'terbit',
                'diterbitkan_pada' => Carbon::now()->subDays(7),
            ],
            // Blog 3
            [
                'judul' => 'Seni Menyunting Naskah Sendiri: Belajar Memenggal Kata yang Kita Cintai',
                'slug' => 'seni-menyunting-naskah-sendiri-memenggal-kata',
                'ringkasan' => 'Menyunting bukan soal mencari salah tik semata, melainkan keberanian membuang bagian yang mubazir demi keutuhan cerita.',
                'isi' => "Menulis draf pertama adalah pekerjaan yang menggunakan seluruh perasaan dan luapan imajinasi, namun menyunting adalah pekerjaan tukang kayu yang menuntut ketelitian pisau bedah nalar.\n\nSering kali kita enggan menghapus sebuah paragraf berbunga-bunga hanya karena merasa kalimat tersebut terdengar puitis dan indah. Padahal, jika paragraf itu tidak menggerakkan jalan cerita atau tidak memperdalam watak tokoh, keberadaannya justru menjadi beban yang membosankan pembaca.\n\nBerlatihlah membaca naskahmu sendiri seolah-olah kamu adalah pembaca yang dingin dan kritis. Potonglah cabang-cabang kalimat yang menjalar liar agar batang utama ceritamu dapat berdiri tegak dan menjulang kokoh.",
                'jenis' => 'blog',
                'kategori_id' => $kategoriMap['kepenulisan'],
                'penulis' => 'Bramantyo Wardhana',
                'status' => 'terbit',
                'diterbitkan_pada' => Carbon::now()->subDays(5),
            ],
            // Blog 4
            [
                'judul' => 'Membaca Ulang Buku Puisi Tipis di Bawah Teduhnya Pohon Beringin Kampus',
                'slug' => 'membaca-ulang-buku-puisi-tipis-di-bawah-pohon-beringin',
                'ringkasan' => 'Catatan pembacaan intim mengenai ritme bait puisi yang mampu memberi jeda di tengah riuhnya jadwal perkuliahan.',
                'isi' => "Siang hari di antara jeda pergantian jam kuliah sering kali membuat kepala pening oleh deretan rumus dan slide materi presentasi yang padat. Di sudut taman kampus dekat fakultas kedokteran, bangku semen di bawah naungan pohon beringin tua selalu menjadi tempat persembunyian terbaik.\n\nDi tempat itulah saya membuka kembali buku puisi tipis yang telah lusuh sudut-sudutnya. Berbeda dengan membaca buku pelajaran yang menuntut hafalan teknis, membaca puisi adalah proses membiarkan diri kita meresapi lambatnya detak detik waktu.\n\nSatu baris sajak yang tepat kadang mampu menjelaskan kegalauan batin yang telah berminggu-minggu gagal kita terjemahkan ke dalam bahasa percakapan sehari-hari.",
                'jenis' => 'blog',
                'kategori_id' => $kategoriMap['resensi'],
                'penulis' => 'Ratna Dewi Kusuma',
                'status' => 'terbit',
                'diterbitkan_pada' => Carbon::now()->subDays(3),
            ],
            // Blog 5
            [
                'judul' => 'Mendengarkan Cerita Orang Biasa: Sumber Ide yang Tak Pernah Kering',
                'slug' => 'mendengarkan-cerita-orang-biasa-sumber-ide-tak-pernah-kering',
                'ringkasan' => 'Inspirasi menulis prosa fiksi kerap kali tersembunyi pada obrolan sederhana penjaga kantin dan tukang fotokopi kampus.',
                'isi' => "Banyak pengarang pemula memeras otak mencari konflik fiksi yang spektakuler, seperti petualangan ke negeri antah-berantah atau konspirasi rahasia dunia. Padahal di sekitar selasar kampus kita, kehidupan sehari-hari menyimpan drama manusia yang luar biasa kaya.\n\nCoba luangkan waktu sejenak untuk duduk di warung kopi belakang kampus dan dengarkan obrolan ibu kantin tentang perjuangan menyekolahkan anaknya, atau dengarkan keluh kesah petugas fotokopi yang hapal jam ujian setiap jurusan.\n\nSastra yang menyentuh hati selalu berakar pada kemanusiaan yang nyata. Dengan membuka telinga dan empati bagi mereka yang sering tak terlihat, kita tak akan pernah kehabisan bahan untuk menyalakan api cerita.",
                'jenis' => 'blog',
                'kategori_id' => $kategoriMap['kepenulisan'],
                'penulis' => 'Hendra Saputro',
                'status' => 'terbit',
                'diterbitkan_pada' => Carbon::now()->subDay(),
            ],
        ];

        foreach ($artikelList as $item) {
            Artikel::updateOrCreate(['slug' => $item['slug']], $item);
        }

        // -------------------------------------------------------------
        // 3. Karya Sastra (8 Karya: minimal 4 terbit, 1 perlu_revisi, dikirim, ditinjau, ditolak)
        // -------------------------------------------------------------
        $karyaList = [
            // Karya 1 (Puisi - Terbit)
            [
                'judul' => 'Catatan pada Daun Jati',
                'jenis' => 'puisi',
                'nama_penulis' => 'Sekar Ayu Kinanti',
                'email' => 'sekar.ayu@student.univ.ac.id',
                'status' => StatusKarya::TERBIT,
                'isi' => "Gugur selembar di pelataran selasar\nuratnya bercerita tentang kemarau panjang\nyang menyepuh debu pada tapak sepatu kita.\n\nKita pernah menitipkan janji di balik jendela perpustakaan\nsebelum waktu melipat jarak menjadi sunyi yang tebal.\nAngin sore berbisik pelan di sela ranting:\ntak ada yang benar-benar hilang dari ingatan\nsungai kenangan hanya mengalir ke muara yang lain.",
                'catatan_redaksi' => 'Diksi imaji visual sangat kuat dan padu. Layak terbit.',
                'riwayat' => [
                    ['status' => StatusKarya::DIKIRIM, 'catatan' => 'Karya berhasil dikirimkan oleh penulis.'],
                    ['status' => StatusKarya::DITINJAU, 'catatan' => 'Naskah masuk meja kurator puisi.'],
                    ['status' => StatusKarya::TERBIT, 'catatan' => 'Puisi lolos kurasi dan resmi diterbitkan di portal.'],
                ],
            ],

            // Karya 2 (Puisi - Terbit)
            [
                'judul' => 'Gerimis di Selasar Perpustakaan',
                'jenis' => 'puisi',
                'nama_penulis' => 'Dimas Wicaksono',
                'email' => 'dimas.w@student.univ.ac.id',
                'status' => StatusKarya::TERBIT,
                'isi' => "Rintik gerimis jatuh mencium kaca buram\n aroma kertas tua bangkit dari rak-rak sunyi.\nDi sini kita menimbun kata demi kata\nmencari penawar bagi kepala yang letih berdebat.\n\nLonceng petang berdentang pelan dari kubah kampus\nbuku catatan terkatup dengan hembusan nafas lega.\nBiar hujan membersihkan jejak langkah yang bimbang\nbesok kita kembali menatap cakrawala.",
                'catatan_redaksi' => 'Suasana liris terbangun dengan indah dan hangat.',
                'riwayat' => [
                    ['status' => StatusKarya::DIKIRIM, 'catatan' => 'Karya dikirimkan ke redaksi Aksara Kampus.'],
                    ['status' => StatusKarya::DITINJAU, 'catatan' => 'Pemeriksaan rima dan tipografi oleh redaktur.'],
                    ['status' => StatusKarya::TERBIT, 'catatan' => 'Karya disetujui terbit.'],
                ],
            ],

            // Karya 3 (Cerpen - Terbit)
            [
                'judul' => 'Lorong Waktu di Toko Buku Tua',
                'jenis' => 'cerpen',
                'nama_penulis' => 'Nadia Kirana',
                'email' => 'nadia.kirana@student.univ.ac.id',
                'status' => StatusKarya::TERBIT,
                'isi' => "Pak Usman memutar kunci kuningan pada pintu kayu toko buku kecilnya di gang sempit dekat kampus barat. Di dalam, tumpukan buku bekas tersusun hingga hampir menyentuh langit-langit yang berdebu tipis. Bagi kebanyakan mahasiswa modern yang terbiasa membaca lewat gawai pintar, tempat ini tampak seperti gudang usang tak bernilai.\n\nNamun bagi Tania, toko buku Pak Usman adalah oase rahasia. Di rak paling pojok bagian sastra terjemahan, ia menemukan sebuah buku bersampul kain biru tua. Ketika membuka halaman pertama, terselip selembar surat bertinta ungu dengan tulisan tangan tertanggal 14 November 1989. Surat itu berisi ungkapan rindu seorang mahasiswa arsitektur kepada kawan diskusinya yang tak pernah kembali setelah demonstrasi damai.\n\nTania membaca surat itu dengan jemari yang bergetar pelan. Di sela aroma kertas lapuk dan derit kipas angin tua di sudut ruangan, ia tersadar bahwa sejarah manusia tak hanya dicatat pada lembar berita utama, melainkan terpatri abadi pada lembaran rahasia yang setia menunggu untuk ditemukan.",
                'catatan_redaksi' => 'Penulisan narasi lancar dengan pembangun suasana yang sangat hidup.',
                'riwayat' => [
                    ['status' => StatusKarya::DIKIRIM, 'catatan' => 'Karya cerpen mini dikirimkan oleh penulis.'],
                    ['status' => StatusKarya::DITINJAU, 'catatan' => 'Pemeriksaan struktur cerita dan penokohan.'],
                    ['status' => StatusKarya::TERBIT, 'catatan' => 'Cerpen disetujui untuk dipublikasikan pada rubrik prosa.'],
                ],
            ],

            // Karya 4 (Esai - Terbit)
            [
                'judul' => 'Pena yang Menolak Bungkam: Sastra Kampus Sebagai Ruang Dialektika',
                'jenis' => 'esai',
                'nama_penulis' => 'Rian Mahendra',
                'email' => 'rian.mahendra@student.univ.ac.id',
                'status' => StatusKarya::TERBIT,
                'isi' => "Keberadaan majalah dinding, buletin cetak, dan portal sastra digital di lingkungan universitas bukanlah sekadar wadah hobi bagi sekelompok mahasiswa pemimpi. Sepanjang catatan sejarah pergerakan kaum terpelajar di tanah air, karya sastra senantiasa menjadi barometer moral dan intelektual masyarakat.\n\nKetika bahasa birokrasi dan wacana kekuasaan cenderung menyederhanakan kenyataan hidup yang majemuk menjadi deretan angka dan slogan, sastranyalah yang datang memulihkan rasa empati. Melalui sajak, cerpen, dan tulisan esai mendalam, mahasiswa belajar menatap wajah-wajah rakyat jelata yang terabaikan oleh megahnya pilar pembangunan fisik.\n\nMenjaga denyut sastra kampus tetap berdetak kencang adalah ikhtiar merawat nurani akal budi kita. Tanpa sastra, kampus berisiko mencetak sarjana-sarjana terampil berhati dingin yang mahir menjalankan sistem namun gamang memaknai hakikat keadilan dan kemanusiaan.",
                'catatan_redaksi' => 'Gagasan bernas dan argumentatif dengan bahasa lugas berkarakter.',
                'riwayat' => [
                    ['status' => StatusKarya::DIKIRIM, 'catatan' => 'Naskah esai diterima sistem.'],
                    ['status' => StatusKarya::DITINJAU, 'catatan' => 'Telaah argumentasi dan logika teks oleh dewan editor.'],
                    ['status' => StatusKarya::TERBIT, 'catatan' => 'Esai dinyatakan lolos kurasi penuh dan diterbitkan.'],
                ],
            ],

            // Karya 5 (Cerpen - Perlu Revisi)
            [
                'judul' => 'Melangkah Menembus Kabut Pagi',
                'jenis' => 'cerpen',
                'nama_penulis' => 'Aditya Nugroho',
                'email' => 'aditya.n@student.univ.ac.id',
                'status' => StatusKarya::PERLU_REVISI,
                'isi' => "Langkah kaki Bayu bergegas menembus kabut tebal yang menyelimuti lereng bukit kampus pagi itu. Hawa dingin menusuk hingga ke balik jaket parasutnya yang tipis. Ia harus tiba di laboratorium sebelum jarum jam menunjuk angka tujuh, membawa sampel tanah yang telah ia amati semalaman di stasiun penelitian cuaca.\n\nDi pertigaan jalan setapak berbatu, langkahnya mendadak terhenti oleh suara aneh di semak belukar. Sesosok bayangan tampak bergerak pelan di balik rimbunnya pohon pinus. Bayu menahan napas dan merapatkan punggungnya ke batang pohon terdekat sembari menatap tajam ke arah kabut yang kian pekat.",
                'catatan_redaksi' => 'Alur paruh kedua melompat terlalu cepat, mohon perjelas motivasi tokoh utama dan perbaiki tanda baca dialog pada percakapan menjelang klimaks.',
                'riwayat' => [
                    ['status' => StatusKarya::DIKIRIM, 'catatan' => 'Naskah cerpen dikirimkan oleh penulis.'],
                    ['status' => StatusKarya::DITINJAU, 'catatan' => 'Pembacaan draf oleh tim kurasi prosa.'],
                    ['status' => StatusKarya::PERLU_REVISI, 'catatan' => 'Alur paruh kedua melompat terlalu cepat, mohon perjelas motivasi tokoh utama dan perbaiki tanda baca dialog pada percakapan menjelang klimaks.'],
                ],
            ],

            // Karya 6 (Puisi - Ditinjau)
            [
                'judul' => 'Menjahit Bayang Petang',
                'jenis' => 'puisi',
                'nama_penulis' => 'Farhan Syahputra',
                'email' => 'farhan.s@student.univ.ac.id',
                'status' => StatusKarya::DITINJAU,
                'isi' => "Matahari tersangkut di kawat menara pemancar\nmenyisakan tembaga jingga pada pucuk-pucuk gedung kuliah.\nSeorang mahasiswa melipat kertas tugas yang kusut\nsambil membayangkan perjalanan pulang yang panjang di kereta malam.\n\nDi antara desis roda besi dan aroma asap gerbong\nkita menjahit bayang-bayang harapan yang koyak oleh letih harian.",
                'catatan_redaksi' => null,
                'riwayat' => [
                    ['status' => StatusKarya::DIKIRIM, 'catatan' => 'Karya berhasil dikirimkan oleh penulis.'],
                    ['status' => StatusKarya::DITINJAU, 'catatan' => 'Sedang diperiksa oleh dewan kurator puisi Aksara Kampus.'],
                ],
            ],

            // Karya 7 (Esai - Dikirim)
            [
                'judul' => 'Estetika Diksi dalam Sastra Digital Masa Kini',
                'jenis' => 'esai',
                'nama_penulis' => 'Lestari Wulandari',
                'email' => 'lestari.w@student.univ.ac.id',
                'status' => StatusKarya::DIKIRIM,
                'isi' => "Perkembangan media sosial telah mengubah cara generasi muda memproduksi dan menikmati teks karya sastra. Hadirnya platform mikroblog melahirkan gaya penulisan yang ringkas, berorientasi pada daya sengat visual, dan mengandalkan keterhubungan emosi secara instan dengan pembaca.\n\nFenomena ini menghadirkan perdebatan seru di kalangan pengamat kebudayaan. Apakah keringkasan diksi di ruang maya mengurangi bobot kedalaman sastra, ataukah justru memperluas jangkauan pembacaan bagi khalayak yang sebelumnya enggan membuka buku tebal?",
                'catatan_redaksi' => null,
                'riwayat' => [
                    ['status' => StatusKarya::DIKIRIM, 'catatan' => 'Karya berhasil dikirimkan ke redaksi dan menunggu peninjauan.'],
                ],
            ],

            // Karya 8 (Cerpen - Ditolak)
            [
                'judul' => 'Rahasia Kotak Kayu di Atap Kampus',
                'jenis' => 'cerpen',
                'nama_penulis' => 'Bagas Saputra',
                'email' => 'bagas.saputra@student.univ.ac.id',
                'status' => StatusKarya::DITOLAK,
                'isi' => 'Malam itu hujan turun deras mengguyur atap gedung rektorat lama. Doni nekat menaiki tangga darurat berkarat menuju lantai paling atas untuk membuktikan rumor mistis yang beredar luas di grup obrolan mahasiswa. Di sudut ventilasi yang pengap, sebuah kotak kayu berukir aneh tergeletak terkunci rapat di bawah remang lampu jalanan.',
                'catatan_redaksi' => 'Gagasan premis cukup menarik, namun naskah ini masih perlu pendalaman konflik yang lebih kuat dan resolusi cerita belum tuntas.',
                'riwayat' => [
                    ['status' => StatusKarya::DIKIRIM, 'catatan' => 'Karya dikirimkan oleh pemohon.'],
                    ['status' => StatusKarya::DITINJAU, 'catatan' => 'Pemeriksaan naskah oleh kurator fiksi.'],
                    ['status' => StatusKarya::DITOLAK, 'catatan' => 'Gagasan premis cukup menarik, namun naskah ini masih perlu pendalaman konflik yang lebih kuat dan resolusi cerita belum tuntas.'],
                ],
            ],
        ];

        foreach ($karyaList as $item) {
            $slug = Karya::generateUniqueSlug($item['judul']);

            $karya = Karya::create([
                'slug' => $slug,
                'judul' => $item['judul'],
                'jenis' => $item['jenis'],
                'isi' => $item['isi'],
                'nama_penulis' => $item['nama_penulis'],
                'email' => $item['email'],
                'status' => $item['status'],
                'catatan_redaksi' => $item['catatan_redaksi'],
            ]);

            if (empty($karya->nomor_kiriman)) {
                $karya->nomor_kiriman = Karya::generateNomorKiriman($karya->id, $karya->created_at);
                $karya->saveQuietly();
            }

            foreach ($item['riwayat'] as $r) {
                RiwayatKarya::create([
                    'karya_id' => $karya->id,
                    'status' => $r['status'],
                    'catatan' => $r['catatan'],
                ]);
            }
        }

        // -------------------------------------------------------------
        // 4. Agenda Sastra (5 Agenda: Campur masa lalu & mendatang)
        // -------------------------------------------------------------
        $agendaList = [
            // 1. Lampau: Workshop
            [
                'judul' => 'Workshop Penulisan Puisi Liris: Menangkap Imaji Sehari-hari',
                'slug' => 'workshop-penulisan-puisi-liris-menangkap-imaji',
                'jenis' => 'workshop',
                'deskripsi' => 'Pelatihan intensif membedah metafora dan rima bersama penyair kampus. Peserta diajak berlatih mengamati fenomena kecil di sekitar ruang kuliah dan meramunya menjadi baris-baris sajak yang bernyawa.',
                'lokasi' => 'Ruang Teater Mini Kampus Barat',
                'mulai_pada' => Carbon::now()->subMonths(2)->setTime(9, 0),
                'selesai_pada' => Carbon::now()->subMonths(2)->setTime(15, 0),
                'kuota' => 30,
            ],
            // 2. Lampau: Bedah Buku
            [
                'judul' => 'Bedah Naskah Kumpulan Esai Mahasiswa: Tafsir Suara Muda',
                'slug' => 'bedah-naskah-kumpulan-esai-mahasiswa',
                'jenis' => 'bedah_buku',
                'deskripsi' => 'Sesi dialog kritis membedah karya kumpulan esai pemikiran mahasiswa angkatan 2024. Menghadirkan dosen pembanding dan perwakilan pegiat pers mahasiswa.',
                'lokasi' => 'Aula Perpustakaan Pusat Lantai 2',
                'mulai_pada' => Carbon::now()->subMonth()->setTime(13, 30),
                'selesai_pada' => Carbon::now()->subMonth()->setTime(17, 0),
                'kuota' => 50,
            ],
            // 3. Mendatang: Lomba
            [
                'judul' => 'Sayembara Cipta Cerpen Nasional Mahasiswa 2026',
                'slug' => 'sayembara-cipta-cerpen-nasional-mahasiswa-2026',
                'jenis' => 'lomba',
                'deskripsi' => 'Kompetisi penulisan cerita pendek bergengsi tahunan bertema Jejak Kemanusiaan. Terbuka bagi seluruh mahasiswa aktif program sarjana dan diploma dari seluruh Indonesia.',
                'lokasi' => 'Daring via Portal Aksara Kampus & Gedung Serbaguna',
                'mulai_pada' => Carbon::now()->addDays(15)->setTime(8, 0),
                'selesai_pada' => Carbon::now()->addDays(35)->setTime(23, 59),
                'kuota' => null, // Terbuka umum
            ],
            // 4. Mendatang: Open Mic
            [
                'judul' => 'Malam Purnama Kata: Panggung Puisi Terbuka di Amphitheater',
                'slug' => 'malam-purnama-kata-panggung-puisi-terbuka',
                'jenis' => 'open_mic',
                'deskripsi' => 'Panggung ekspresi seni bebas di bawah terang bulan purnama. Seluruh mahasiswa dan sivitas akademika diperbolehkan membacakan karya puisi, monolog singkat, atau musikalisasi sajak secara spontan.',
                'lokasi' => 'Amphitheater Taman Rektorat Kampus',
                'mulai_pada' => Carbon::now()->addDays(20)->setTime(19, 0),
                'selesai_pada' => Carbon::now()->addDays(20)->setTime(22, 0),
                'kuota' => 100,
            ],
            // 5. Mendatang: Workshop
            [
                'judul' => 'Kelas Intensif Menulis Opini dan Esai Populer Media Massa',
                'slug' => 'kelas-intensif-menulis-opini-esai-populer',
                'jenis' => 'workshop',
                'deskripsi' => 'Bimbingan taktis merumuskan argumen yang tajam dan memikat redaksi koran serta majalah nasional. Menghadirkan jurnalis opini senior sebagai mentor pendamping.',
                'lokasi' => 'Laboratorium Multimedia Lantai 3',
                'mulai_pada' => Carbon::now()->addDays(28)->setTime(9, 0),
                'selesai_pada' => Carbon::now()->addDays(29)->setTime(16, 0),
                'kuota' => 25,
            ],
        ];

        foreach ($agendaList as $item) {
            Agenda::updateOrCreate(['slug' => $item['slug']], $item);
        }
    }
}
