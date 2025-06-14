<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Page;
use Illuminate\Http\Request;
use DB;
use Carbon\Carbon;
use App\Models\Berita;
use App\Models\StatistikPengunjung;

class PageController extends Controller
{
    public function home()
    {
        $today = Carbon::now()->format('d');
        $bulanIni = Carbon::now()->format('m');

        $statistikPengunjung = StatistikPengunjung::find(1);
        $tambah = $statistikPengunjung->dilihat + 1;

        $tglDb = $statistikPengunjung->created_at->format('d');
        $bulanDb = $statistikPengunjung->created_at->format('m');
        $hariIni = ($today != $tglDb) ? 1 : $statistikPengunjung->hari_ini + 1;
        $bulanIniDb = ($bulanIni != $bulanDb) ? $bulanIni : $statistikPengunjung->bulan_ini + 1;

        $statistikPengunjung->update([
            'dilihat' => $tambah,
            'hari_ini' => $hariIni,
            'bulan_ini' => $bulanIniDb,
            'created_at' => Carbon::now(),
        ]);

        $beritaTerbaru = Berita::latest('tanggal')->paginate(8);
        $websiteSkpds = DB::table('website_skpds')->latest('id')->limit(5)->get();
        $imageSlider = DB::table('sliders')->latest('id')->limit(5)->get();
        $galeri = DB::table('galeris')->latest('id')->limit(4)->get();
        $video = DB::table('video_kegiatans')->latest('created_at')->limit(12)->get();
        $latestRecord = Berita::latest('tanggal')->first();
        $beritaTerbaruNew = Berita::latest('tanggal')->skip(1)->take(2)->get();

        $infografis = DB::table('infografis')->latest('id')->limit(4)->get();
        $popup = DB::table('popup')->latest('created_at')->first();

        return view('frontend.home.index', compact(
            'beritaTerbaru', 'popup', 'beritaTerbaruNew', 'latestRecord', 'websiteSkpds', 'infografis', 'imageSlider', 'galeri', 'video'
        ));
    }

    public function page($slug)
    {
        $page = Page::where('slug', $slug)->firstOrFail();
        $beritaTerbaru = Berita::latest('created_at')->limit(5)->get();

        return view('frontend.home.page', [
            'slug' => $slug,
            'judul' => $page->judul,
            'isi' => $page->isi,
            'created_at' => $page->created_at,
            'beritaTerbaru' => $beritaTerbaru,
        ]);
    }

    public function downloadFile($file)
    {
        $filePath = public_path("frontend/infografis/file/".$file);
        return response()->download($filePath, null, ['Content-Type: application/pdf']);
    }

    public function show($file)
    {
        $filePath = public_path("frontend/infografis/file/".$file);
        return response()->file($filePath, ['content-type' => 'application/pdf']);
    }

    public function tampil($slug)
    {
        $content = $this->getContentBySlug($slug);

        return $this->home()->with('content', $content)->with('slug', $slug);
    }

    public function index()
    {
        $infografis = DB::table('infografis')->latest('id')->get();
        return view('frontend.infografis.index', compact('infografis'));
    }

    private function getContentBySlug($slug)
    {
        $contents = [
            'profil-batanghari' => 'Content of Profil Batanghari page',
            'sejarah' => 'Content of Sejarah page',
            'arti-lambang' => 'Content of Arti Lambang page',
            'kondisi-demografi' => 'Content of Kondisi Demografi page',
            'peta-batanghari' => 'Content of Peta Batanghari page',
            'visi-dan-misi' => 'Content of Visi & Misi page',
            'pemerintah-batanghari' => 'Content of Pemerintahan Batanghari page',
            'akuntabiltas-pemerintahan' => 'Content of Akuntabiltas Pemerintahan page',
            'akuntabiltas-pelaporan' => 'Content of Akuntabiltas Pelaporan page',
            'akuntabilitas-batanghari' => 'Content of Transparansi Anggaran page',
            'struktur-organisasi' => 'Content of Struktur Organisasi page',
            'profil-direktur-rsud' => 'Content of Profil Direktur RSUD page',
            'standar-pelayanan-publik' => 'Content of Standar Pelayanan Publik page',
            'maklumat-pelayanan' => 'Content of Maklumat Pelayanan page',
            'profil-rsud' => 'Content of Profil RSUD page',
            'penghargaan' => 'Content of Penghargaan page',
            'denah-dan-lokasi' => 'Content of Denah dan Lokasi page',
            'rawat-jalan' => 'Content of Rawat Jalan page',
            'rawat-inap' => 'Content of Rawat Inap page',
            'pelayanan-penunjang' => 'Content of Pelayanan Penunjang page',
            'fasilitas' => 'Content of Fasilitas page',
            'survei-kepuasan-masyarakat' => 'Content of Survei Kepuasan Masyarakat page',
            // 'alur-pendaftaran-pasien' => 'Content of Alur Pendaftaran Pasien page',
            'tata-tertib-dan-jam-berkunjung' => 'Content of Tata Tertib dan Jam Berkunjung page',
            // 'jadwal-poliklinik' => 'Content of Jadwal Poliklinik page',
            'tarif-pelayanan' => 'Content of Tarif Pelayanan page',
            'rujukan-gawat-darurat' => 'Content of Rujukan Gawat Darurat page',
            'jadwal-dokter' => 'Content of Jadwal Dokter page',
            'informasi-publik' => 'Content of Informasi Publik page',
            'standar-pelayanan' => 'Content of Standar Pelayanan page',
            'alur-pelayanan' => 'Content of Alur Pelayanan page',

        ];

        return $contents[$slug] ?? 'Content not found';
    }
}
