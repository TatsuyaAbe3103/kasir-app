@extends('layouts.app')

@section('title', 'Edit Menu - Nusa POS')

@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-6" x-data="imageUploader('{{ $product->image }}')">

    <!-- Back & Header -->
    <div class="flex items-center space-x-3">
        <a href="{{ route('admin.products.index') }}" class="w-9 h-9 rounded-xl bg-white border border-slate-200 text-slate-700 flex items-center justify-center hover:bg-slate-50 transition">
            <i class="fa-solid fa-arrow-left text-xs"></i>
        </a>
        <div>
            <h1 class="text-xl font-black text-slate-900 tracking-tight">Edit Menu: {{ $product->name }}</h1>
            <p class="text-xs text-slate-500">Perbarui harga, informasi menu, atau foto.</p>
        </div>
    </div>

    <!-- Form Card -->
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm p-6 sm:p-8">
        <form method="POST" action="{{ route('admin.products.update', $product->id) }}" enctype="multipart/form-data" class="space-y-5">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <!-- Kode SKU -->
                <div>
                    <label class="text-xs font-bold text-slate-700 uppercase tracking-wider block mb-1">Kode Menu / SKU *</label>
                    <input 
                        type="text" 
                        name="code" 
                        value="{{ old('code', $product->code) }}" 
                        required 
                        class="w-full text-xs py-2.5 px-3 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-amber-500 @error('code') border-rose-500 @enderror"
                    >
                    @error('code') <span class="text-[10px] text-rose-500 font-bold">{{ $message }}</span> @enderror
                </div>

                <!-- Kategori -->
                <div>
                    <label class="text-xs font-bold text-slate-700 uppercase tracking-wider block mb-1">Kategori *</label>
                    <select 
                        name="category_id" 
                        required 
                        class="w-full text-xs py-2.5 px-3 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-amber-500"
                    >
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ old('category_id', $product->category_id) == $cat->id ? 'selected' : '' }}>
                                {{ $cat->icon }} {{ $cat->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Nama Menu -->
            <div>
                <label class="text-xs font-bold text-slate-700 uppercase tracking-wider block mb-1">Nama Menu *</label>
                <input 
                    type="text" 
                    name="name" 
                    value="{{ old('name', $product->name) }}" 
                    required 
                    class="w-full text-xs py-2.5 px-3 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-amber-500"
                >
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <!-- Harga Modal (HPP) -->
                <div>
                    <label class="text-xs font-bold text-slate-700 uppercase tracking-wider block mb-1">Harga Modal / HPP (Rp)</label>
                    <input 
                        type="number" 
                        name="cost_price" 
                        value="{{ old('cost_price', $product->cost_price) }}" 
                        min="0" 
                        class="w-full text-xs py-2.5 px-3 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-amber-500"
                    >
                </div>

                <!-- Harga Jual -->
                <div>
                    <label class="text-xs font-bold text-slate-700 uppercase tracking-wider block mb-1">Harga Jual (Rp) *</label>
                    <input 
                        type="number" 
                        name="price" 
                        value="{{ old('price', $product->price) }}" 
                        required 
                        min="0" 
                        class="w-full text-xs py-2.5 px-3 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-amber-500"
                    >
                </div>
            </div>

            <!-- FOTO MENU: BISA GANTI FILE GAMBAR ATAU LINK URL -->
            <div class="space-y-2">
                <label class="text-xs font-bold text-slate-700 uppercase tracking-wider block">Foto Menu</label>
                
                <div class="border-2 border-dashed border-slate-200 rounded-2xl p-4 bg-slate-50 hover:bg-slate-100/60 transition">
                    <div class="flex flex-col sm:flex-row items-center gap-4">
                        
                        <!-- Preview Box -->
                        <div class="w-24 h-24 rounded-2xl bg-white border border-slate-200 overflow-hidden flex items-center justify-center flex-shrink-0 shadow-sm relative">
                            <template x-if="previewUrl">
                                <img :src="previewUrl" alt="Preview" class="w-full h-full object-cover">
                            </template>
                            <template x-if="!previewUrl">
                                <div class="text-center text-slate-300">
                                    <i class="fa-solid fa-image text-2xl"></i>
                                    <span class="block text-[9px] mt-1 text-slate-400">Pratinjau</span>
                                </div>
                            </template>
                        </div>

                        <!-- Upload Controls -->
                        <div class="flex-1 w-full space-y-2">
                            <div>
                                <label class="text-[11px] font-bold text-slate-700 block mb-1">
                                    <i class="fa-solid fa-cloud-arrow-up mr-1 text-amber-500"></i> Pilih File Gambar Baru dari Komputer:
                                </label>
                                <input 
                                    type="file" 
                                    name="image_file" 
                                    accept="image/*"
                                    @change="handleFileSelect($event)"
                                    class="w-full text-xs text-slate-500 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-amber-500 file:text-slate-950 hover:file:bg-amber-600 file:cursor-pointer cursor-pointer"
                                >
                            </div>

                            <div class="pt-1">
                                <span class="text-[10px] text-slate-400 block mb-1">Atau gunakan Link URL Gambar:</span>
                                <input 
                                    type="url" 
                                    name="image_url" 
                                    x-model="urlInput"
                                    @input="if(!selectedFile) previewUrl = urlInput"
                                    placeholder="https://..." 
                                    class="w-full text-xs py-1.5 px-3 bg-white border border-slate-200 rounded-xl focus:outline-none focus:border-amber-500"
                                >
                            </div>
                        </div>

                    </div>
                </div>
            </div>

            <!-- Deskripsi -->
            <div>
                <label class="text-xs font-bold text-slate-700 uppercase tracking-wider block mb-1">Deskripsi Menu</label>
                <textarea 
                    name="description" 
                    rows="3" 
                    class="w-full text-xs py-2 px-3 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-amber-500"
                >{{ old('description', $product->description) }}</textarea>
            </div>

            <!-- Ketersediaan Checkbox -->
            <div class="pt-1">
                <label class="inline-flex items-center space-x-2 cursor-pointer">
                    <input type="checkbox" name="is_available" value="1" {{ $product->is_available ? 'checked' : '' }} class="rounded text-amber-500 focus:ring-0">
                    <span class="text-xs font-bold text-slate-800">Menu Tersedia (Ready) di layar kasir</span>
                </label>
            </div>

            <!-- Submit Button -->
            <div class="pt-4 border-t border-slate-100 flex items-center justify-end space-x-3">
                <a href="{{ route('admin.products.index') }}" class="px-5 py-2.5 rounded-xl border border-slate-200 text-xs font-bold text-slate-600 hover:bg-slate-50 transition">
                    Batal
                </a>
                <button type="submit" class="px-6 py-2.5 bg-amber-500 hover:bg-amber-600 text-slate-950 text-xs font-black rounded-xl transition shadow-md shadow-amber-500/20">
                    Simpan Perubahan
                </button>
            </div>

        </form>
    </div>

</div>
@endsection

@push('scripts')
<script>
    function imageUploader(initialImage) {
        return {
            previewUrl: initialImage || '',
            urlInput: initialImage || '',
            selectedFile: false,

            handleFileSelect(event) {
                const file = event.target.files[0];
                if (file) {
                    this.selectedFile = true;
                    this.previewUrl = URL.createObjectURL(file);
                }
            }
        };
    }
</script>
@endpush
