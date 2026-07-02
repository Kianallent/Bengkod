<div>
    @if ($errors->any())
        <div class="alert alert-error shadow-lg mb-6 rounded-xl border-l-4 border-red-600">
            <div class="flex items-start gap-3 w-full">
                <i class="fas fa-exclamation-circle text-lg flex-shrink-0 mt-1"></i>
                <div class="flex-1">
                    <h3 class="font-bold text-red-600">{{ $title ?? 'Gagal Menyimpan Data' }}</h3>
                    <ul class="text-sm text-red-600 mt-2 space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>• {{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endif
</div>
