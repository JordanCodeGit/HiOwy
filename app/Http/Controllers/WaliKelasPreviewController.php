<?php

namespace App\Http\Controllers;

use App\Support\WaliKelasPreview;
use Illuminate\View\View;

class WaliKelasPreviewController extends Controller
{
    public function __construct(private readonly WaliKelasPreview $preview) {}

    public function dashboard(): View
    {
        return $this->page('dashboard');
    }

    public function profile(): View
    {
        return $this->page('profile');
    }

    public function summary(int $summary): View
    {
        return $this->page('summary', ['summary' => $this->preview->findSummary($summary)]);
    }

    private function page(string $page, array $data = []): View
    {
        $summaries = $this->preview->summaries();

        return view('walas.'.$page, $data + [
            'summaries' => $summaries,
            'unreadCount' => $summaries->where('status', 'unread')->count(),
            'notifications' => $summaries->where('status', 'unread')->values(),
        ]);
    }
}
