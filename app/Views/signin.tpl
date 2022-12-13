{{layout=default}}
{{section=body}}
<main class="w-screen h-screen flex flex-col items-center justify-center select-none">
    {{include=error}}
    <div class="block p-6 sm:rounded-lg sm:shadow-lg bg-white w-full max-w-sm">
        <form action="/signin" method="post">
            <div class="form-group mb-6">
                <label for="loginInput" class="form-label inline-block mb-2 text-gray-700">{{lang=signin_login}}</label>
                <input type="text" class="form-control
            block
            w-full
            px-3
            py-1.5
            text-base
            font-normal
            text-gray-700
            bg-white bg-clip-padding
            border border-solid border-gray-300
            rounded
            transition
            ease-in-out
            m-0
            focus:text-gray-700 focus:bg-white focus:border-sky-600 focus:outline-none" id="loginInput"
                    placeholder="{{lang=signin_login}}" name="login" required>
            </div>
            <div class="form-group mb-6">
                <label for="passwordInput"
                    class="form-label inline-block mb-2 text-gray-700">{{lang=signin_password}}</label>
                <input type="password" class="form-control block
            w-full
            px-3
            py-1.5
            text-base
            font-normal
            text-gray-700
            bg-white bg-clip-padding
            border border-solid border-gray-300
            rounded
            transition
            ease-in-out
            m-0
            focus:text-gray-700 focus:bg-white focus:border-sky-600 focus:outline-none" id="passwordInput"
                    placeholder="{{lang=signin_password}}" name="password" required>
            </div>
            <div class="flex justify-between items-center mb-6">
                <div class="form-group form-check">
                    <input type="checkbox"
                        class="form-check-input appearance-none h-4 w-4 border border-gray-300 rounded-sm bg-white checked:bg-sky-600 checked:border-sky-600 focus:outline-none transition duration-200 mt-1 align-top bg-no-repeat bg-center bg-contain float-left mr-2 cursor-pointer"
                        id="rememberInput" name="remember">
                    <label class="form-check-label inline-block text-gray-800"
                        for="rememberInput">{{lang=signin_remember}}</label>
                </div>
            </div>
            <button type="submit" class="
          w-full
          px-6
          py-2.5
          bg-sky-600
          text-white
          font-medium
          text-xs
          leading-tight
          uppercase
          rounded
          shadow-md
          hover:bg-sky-700 hover:shadow-lg
          focus:bg-sky-700 focus:shadow-lg focus:outline-none focus:ring-0
          active:bg-sky-800 active:shadow-lg
          transition
          duration-150
          ease-in-out">{{lang=signin_submit}}</button>
        </form>
    </div>
</main>
{{/section}}