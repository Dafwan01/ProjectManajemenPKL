<div style="background-image:url('{{ asset('images/Balkot.png') }}')" class="min-h-screen w-full flex items-center justify-center bg-cover bg-center bg-no-repeat">
   
    <div class="w-full max-w-sm p-4 bg-white border border-gray-200 rounded-lg shadow-sm sm:p-6 md:p-8">
        <form class="space-y-6" action="#">
            <h5 class="text-xl font-medium text-gray-900">Sign in to our platform</h5>
            
            <div>
                <label for="email" class="block mb-2 text-sm font-medium text-gray-900">Masukan Email</label>
                <input type="email" name="email" id="email" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" placeholder="name@gmail.com" required />
            </div> 
            
            <div>
                <label for="password" class="block mb-2 text-sm font-medium text-gray-900">Masukan Password</label>
                <input type="password" name="password" id="password" placeholder="••••••••" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" required />
            </div>
            
            <div class="flex items-start">
                <div class="flex items-start">
                    <div class="flex items-center h-5">
                        <input id="remember" type="checkbox" value="" class="w-4 h-4 border border-gray-300 rounded-sm bg-gray-50 focus:ring-3 focus:ring-blue-300" required />
                    </div>
                    <label for="remember" class="ms-2 text-sm font-medium text-gray-900">Remember me</label>
                </div>
            </div>
            
            <button wire:click="gotodashboard" class="w-full text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center">Login</button>
        </form>
    </div>

</div>