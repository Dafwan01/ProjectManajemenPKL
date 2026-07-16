<div style="background-image:url('{{ asset('images/Balkot.png') }}')"  class="min-h-screen w-full flex items-center justify-center  bg-cover bg-center bg-no-repeat">
   
   <div class="bg-white p-8 rounded-lg shadow-md w-full max-w-md mx-4">
      <h2 class="text-2xl font-bold mb-6 text-center">Login</h2>
      
      <form wire:submit.prevent="login">
         <div class="mb-4">
            <label for="email" class="block text-gray-700 font-bold mb-2">Email</label>
            <input type="email" id="email" wire:model.defer="email" class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-300 focus:border-blue-500" required>
         </div>
         
         <div class="mb-6">
            <label for="password" class="block text-gray-700 font-bold mb-2">Password</label>
            <input type="password" id="password" wire:model.defer="password" class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-300 focus:border-blue-500" required>
         </div>
         
         <button type="submit" class="w-full bg-blue-500 text-white font-bold py-2 px-4 rounded-lg hover:bg-blue-600 transition duration-300">
            Login
         </button>
      </form>
   </div>

</div>
