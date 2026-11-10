<?php
namespace App\Http\Controllers;

use App\Models\Buku;
use App\Models\Kategori;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Validation\Rule;

class BukuController extends Controller
{
    private function pesan(): array
    {
        return [
            'required' => ':attribute wajib diisi.',
            'max' => ':attribute tidak boleh lebih dari :max karakter.',
            'unique' => ':attribute sudah digunakan.',
            'exists' => ':attribute yang dipilih tidak valid.',
        ];
    }

    private function atribut(): array
    {
        return [
            'kode' => 'Kode buku',
            'judul' => 'Judul',
            'kategori_id' => 'Kategori',
        ];
    }

    public function index(): View
    {
        return view('buku.index', [
            'bukus' => Buku::with('kategori')->orderBy('id')->get()
        ]);
    }

    public function create(): View
    {
        return view('buku.form', [
            'buku' => new Buku(),
            'kategoris' => Kategori::orderBy('nama')->get()
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'kode' => ['required', 'max:10', 'unique:bukus,kode'],
            'judul' => ['required', 'max:255'],
            'kategori_id' => ['required', 'exists:kategoris,id'],
        ], $this->pesan(), $this->atribut());

        Buku::create($data);

        return redirect()->route('buku.index')->with('status', 'Buku ditambahkan.');
    }

    public function edit(Buku $buku): View
    {
        return view('buku.form', [
            'buku' => $buku,
            'kategoris' => Kategori::orderBy('nama')->get()
        ]);
    }

    public function update(Request $request, Buku $buku): RedirectResponse
    {
        $data = $request->validate([
            'kode' => ['required', 'max:10', Rule::unique('bukus', 'kode')->ignore($buku->id)],
            'judul' => ['required', 'max:255'],
            'kategori_id' => ['required', 'exists:kategoris,id'],
        ], $this->pesan(), $this->atribut());

        $buku->update($data);

        return redirect()->route('buku.index')->with('status', 'Buku diperbarui.');
    }

    public function destroy(Buku $buku): RedirectResponse
    {
        $buku->delete();

        return redirect()->route('buku.index')->with('status', 'Buku dihapus.');
    }
}