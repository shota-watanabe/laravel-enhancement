<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Companies') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">

                {{ Html::linkRoute('companies.index', '一覧に戻る') }}

                {{ Html::linkRoute('companies.edit', '編集', compact('company')) }}

                <dl>
                    <dt>ID</dt>
                    <dd>{{ $company->id }}</dd>
                    <dt>Name</dt>
                    <dd>{{ $company->name }}</dd>
                    <dt>Created at</dt>
                    <dd>{{ $company->created_at }}</dd>
                    <dt>Updated at</dt>
                    <dd>{{ $company->updated_at }}</dd>
                </dl>
            </div>
        </div>
    </div>
</x-app-layout>
