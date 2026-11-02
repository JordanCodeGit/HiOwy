<?php

namespace App\Http\Controllers;

use App\Support\KesiswaanPreview;
use Illuminate\View\View;

class KesiswaanPreviewController extends Controller
{
    public function __construct(private readonly KesiswaanPreview $preview) {}

    public function dashboard(): View
    {
        return $this->page('dashboard', ['reports' => $this->preview->visibleReports()->where('archived', false)->values()]);
    }

    public function report(int $report): View
    {
        return $this->page('report', ['report' => $this->preview->findVisible($report)]);
    }

    public function archive(): View
    {
        return $this->page('archive', ['reports' => $this->preview->visibleReports()->where('archived', true)->values()]);
    }

    public function statistics(): View
    {
        return $this->page('statistics', ['statistics' => $this->preview->statistics()]);
    }

    public function accessLog(): View
    {
        return $this->page('access-log', ['logs' => $this->preview->accessLogs()]);
    }

    public function profile(): View
    {
        return $this->page('profile');
    }

    public function publicLearn(string $code): View
    {
        abort_unless($code === 'smakita', 404);

        return view('public.learn');
    }

    private function page(string $name, array $data = []): View
    {
        return view('kesiswaan.'.$name, $data + [
            'summary' => $this->preview->summary(),
            'types' => $this->preview->types(),
        ]);
    }
}
