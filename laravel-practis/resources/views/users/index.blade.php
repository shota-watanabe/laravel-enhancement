<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Users') }}
        </h2>
    </x-slot>

    @if($errors->any())
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                @foreach($errors->all() as $error)
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 text-gray-900">
                            {{ $error }}
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    @if (session('status'))
        <div class="alert alert-success">
            {{ session('status') }}
        </div>
    @endif

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                @if(count($users) >= 1)
                    <div class="px-3 py-4">ユーザー一覧</div>
                    <form method="GET" action="{{ route('users.index') }}">
                        <div class="px-4 pb-3 flex space-x-2 items-center">
                            <div>
                                <input name="user_name" class="border border-gray-500 py-2" placeholder="ユーザー名を入力">
                            </div>
                            <div class="px-4">
                                <button
                                    class="ml-auto bg-indigo-50 border-0 py-2 px-6 focus:outline-none hover:bg-indigo-600 rounded">
                                    検索する
                                </button>
                            </div>
                        </div>
                        @if(Auth::user()->isAdmin())
                            <div class="px-4 pb-3 flex space-x-2 items-center">
                                <div>
                                    <input name="company_name" class="border border-gray-500 py-2" placeholder="会社名を入力">
                                </div>
                                <div class="px-4">
                                    <button
                                        class="ml-auto bg-indigo-50 border-0 py-2 px-6 focus:outline-none hover:bg-indigo-600 rounded">
                                        検索する
                                    </button>
                                </div>
                            </div>
                        @endif
                        <div class="px-4 pb-3 flex space-x-2 items-center">
                            <div>
                                <input name="section_name" class="border border-gray-500 py-2" placeholder="部署名を入力">
                            </div>
                            <div class="px-4">
                                <button
                                    class="ml-auto bg-indigo-50 border-0 py-2 px-6 focus:outline-none hover:bg-indigo-600 rounded">
                                    検索する
                                </button>
                            </div>
                        </div>
                    </form>
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                        <tr>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                ID
                            </th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Name
                            </th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Company
                            </th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Section
                            </th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"></th>
                        </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($users as $user)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">{{ $user->id }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">{{ $user->name }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">{{ $user->company->name }}</td>
                                @foreach($user->sections as $section)
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $section->name }}</td>
                                @endforeach
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                    <div class="mt-1 mb-1 flex justify-center">
                        {{ $users->links() }}
                    </div>
                @else
                    <div>ユーザーはいません。</div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
