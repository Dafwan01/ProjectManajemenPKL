<div style="background-image:url('{{ asset('images/Balkot.png') }}')" class="min-h-screen w-full flex items-center justify-center bg-cover bg-center bg-no-repeat">
   
    <div class="w-full max-w-sm p-4 bg-white border border-gray-200 rounded-lg shadow-sm sm:p-6 md:p-8">
        <form class="space-y-6" wire:submit.prevent="login">
            <h5 class="text-xl font-medium text-gray-900">Sign in to our platform</h5>

            @if ($errorMessage)
                <div class="p-3 text-sm text-red-800 bg-red-50 border border-red-200 rounded-lg">
                    {{ $errorMessage }}
                </div>
            @endif
            
            <div>
                <label for="email" class="block mb-2 text-sm font-medium text-gray-900">Masukan Email</label>
                <input 
                    type="email" 
                    wire:model="email" 
                    id="email" 
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 @error('email') border-red-500 @enderror" 
                    placeholder="name@gmail.com" 
                    required 
                />
                @error('email') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
            </div> 
            
            <div>
                <label for="password" class="block mb-2 text-sm font-medium text-gray-900">Masukan Password</label>
                <input 
                    type="password" 
                    wire:model="password" 
                    id="password" 
                    placeholder="••••••••" 
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 @error('password') border-red-500 @enderror" 
                    required 
                />
                @error('password') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
            </div>
            
            <div class="flex items-start">
                <div class="flex items-start">
                    <div class="flex items-center h-5">
                        <input wire:model="remember" id="remember" type="checkbox" class="w-4 h-4 border border-gray-300 rounded-sm bg-gray-50 focus:ring-3 focus:ring-blue-300" />
                    </div>
                    <label for="remember" class="ms-2 text-sm font-medium text-gray-900">Remember me</label>
                </div>
            </div>
            
            <button 
                type="submit"
                wire:loading.attr="disabled"
                wire:target="login"
                class="w-full text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center disabled:opacity-50"
            >
                <span wire:loading.remove wire:target="login">Login</span>
                <span wire:loading wire:target="login">Memproses...</span>
            </button>
        </form>
    </div>

</div>