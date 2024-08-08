@extends('layouts.main')
@section('content')
<section class=" ">
    <div class="flex flex-col items-center justify-start px-6 py-8 mx-auto md:h-screen lg:py-0">
        <a href="#" class="flex items-center mb-6 text-2xl font-semibold text-gray-900 ">
            <img class="w-8 h-8 mr-2" src="https://flowbite.s3.amazonaws.com/blocks/marketing-ui/logo.svg" alt="logo">
            ログイン
        </a>

        <div class="w-full bg-white rounded-lg shadow  md:mt-0 sm:max-w-md xl:p-0  ">
            <div class="mt-8 p-6 space-y-4 md:space-y-6 sm:p-8">

                <form class="space-y-4 md:space-y-6 text-center" action="{{ route('login.store') }}">
                    <div>
                        <label for="email" class="block mb-2 text-sm font-medium text-gray-600 text-left">選択してください。</label>
                        <input type="text" name="user_id" id="" list="users" class="bg-gray-50 border border-gray-300 text-gray-900 rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full  py-2 pl-4" placeholder="ユーザー名を入力してください">
                        <datalist id="users">
                            @foreach($users as $user)
                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                            @endforeach
                        </datalist>
                    </div>


                    <button type="submit" class="w-1/2 outline text-gray-800 bg-primary-600 hover:bg-primary-700 focus:ring-4 focus:outline-none focus:ring-primary-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-primary-600 dark:hover:bg-primary-700 dark:focus:ring-primary-800">Sign in</button>
                    <p class="text-sm font-light text-gray-500 dark:text-gray-400">
                        Don’t have an account yet? <a href="#" class="font-medium text-primary-600 hover:underline dark:text-primary-500">Sign up</a>
                    </p>
                </form>
            </div>
        </div>
    </div>
</section>

@endsection