<?php

namespace Tests\Unit;

use App\Http\Requests\Concerns\DetectsProfileOnlyUpdate;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Tests\TestCase;

class ProfileOnlyUpdateTest extends TestCase
{
    /** @dataProvider updateProvider */
    public function test_exception_is_limited_to_profile_updates(array $changes, bool $expected): void
    {
        $user = new User([
            'name' => 'Usuario Teste',
            'email' => 'teste@example.com',
            'cpf' => '52998224725',
            'telefone' => '11912345678',
            'pessoa_id' => null,
        ]);
        $request = new class extends FormRequest {
            use DetectsProfileOnlyUpdate;

            public function profileOnly(User $user): bool
            {
                return $this->isProfileOnlyUpdate($user);
            }
        };
        $request->merge(array_merge($user->getAttributes(), [
            'cpf' => '529.982.247-25',
            'telefone' => '(11) 91234-5678',
            'pessoa_id' => '',
            'perfil_id' => 2,
        ], $changes));

        $this->assertSame($expected, $request->profileOnly($user));
    }

    public function updateProvider(): array
    {
        return [
            'local profile' => [[], true],
            'admin profiles' => [['perfil_id' => [2, 3], 'instituicao_id' => [10, 20]], true],
            'changed CPF' => [['cpf' => '111.444.777-35'], false],
            'changed name' => [['name' => 'Outro Nome'], false],
            'changed email' => [['email' => 'outro@example.com'], false],
            'changed phone' => [['telefone' => '(21) 91234-5678'], false],
            'changed clergy link' => [['pessoa_id' => 10], false],
            'changed password' => [['password' => 'nova-senha'], false],
        ];
    }
}
