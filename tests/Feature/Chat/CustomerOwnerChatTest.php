<?php

namespace Tests\Feature\Chat;

use App\Models\ChatConversation;
use App\Models\ChatMessage;
use App\Models\Venue;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CustomerOwnerChatTest extends TestCase
{
    use RefreshDatabase;

    private User $customer;
    private User $otherCustomer;
    private User $owner;
    private Venue $venue;

    protected function setUp(): void
    {
        parent::setUp();

        $this->customer = User::factory()->create(['role' => 'customer', 'email_verified_at' => now()]);
        $this->otherCustomer = User::factory()->create(['role' => 'customer', 'email_verified_at' => now()]);
        $this->owner = User::factory()->create(['role' => 'owner', 'email_verified_at' => now()]);

        $this->venue = Venue::create([
            'name'     => 'Sân Của Chủ',
            'slug'     => 'san-cua-chu',
            'owner_id' => $this->owner->id,
            'phone'    => '0900000001',
            'city'     => 'TP.HCM',
            'district' => 'Q1',
            'address'  => '1 Đường Test',
            'status'   => 'active',
        ]);
    }

    public function test_khach_chua_dang_nhap_khong_chat_duoc(): void
    {
        $this->post(route('customer.chat.initiate'))
            ->assertRedirect(route('login'));

        $this->assertEquals(0, ChatConversation::count());
    }

    public function test_owner_khong_dung_duoc_chat_cua_khach(): void
    {
        $this->actingAs($this->owner)
            ->post(route('customer.chat.initiate'))
            ->assertForbidden();
    }

    public function test_nut_chat_voi_chu_san_chi_hien_voi_khach_dang_nhap(): void
    {
        $this->actingAs($this->customer)
            ->get(route('venues.show', $this->venue->slug))
            ->assertOk()
            ->assertSee('Chat với chủ sân');

        auth()->logout();

        $this->get(route('venues.show', $this->venue->slug))
            ->assertOk()
            ->assertDontSee('Chat với chủ sân');
    }

    public function test_nut_chat_gui_slug_khu_san_chu_khong_phai_id(): void
    {
        // Venue bind route theo slug (Venue::getRouteKeyName) → gửi id sẽ 404
        $this->actingAs($this->customer)
            ->get(route('venues.show', $this->venue->slug))
            ->assertOk()
            ->assertSee('slug:', false)
            ->assertDontSee('detail: { id:', false);
    }

    public function test_khach_dang_nhap_tao_duoc_hoi_thoai_ho_tro(): void
    {
        $response = $this->actingAs($this->customer)->post(route('customer.chat.initiate'));

        $response->assertOk();
        $conversation = ChatConversation::first();

        $this->assertEquals('support', $conversation->type);
        $this->assertEquals($this->customer->id, $conversation->user_id);
        $this->assertNull($conversation->session_token);
        // Có tin nhắn chào tự động
        $this->assertEquals(1, $conversation->messages()->count());
    }

    public function test_khach_mo_duoc_hoi_thoai_voi_chu_san(): void
    {
        $this->actingAs($this->customer)
            ->post(route('customer.chat.venue', $this->venue))
            ->assertOk();

        $conversation = ChatConversation::first();
        $this->assertEquals('owner', $conversation->type);
        $this->assertEquals($this->venue->id, $conversation->venue_id);
        $this->assertEquals($this->owner->id, $conversation->owner_id);
    }

    public function test_mo_lai_hoi_thoai_cu_khong_tao_them(): void
    {
        $this->actingAs($this->customer)->post(route('customer.chat.venue', $this->venue))->assertOk();
        $this->actingAs($this->customer)->post(route('customer.chat.venue', $this->venue))->assertOk();

        $this->assertEquals(1, ChatConversation::count());
    }

    public function test_khach_khong_doc_duoc_hoi_thoai_cua_nguoi_khac(): void
    {
        $conversation = ChatConversation::create([
            'user_id'        => $this->otherCustomer->id,
            'type'           => 'support',
            'customer_name'  => $this->otherCustomer->name,
            'status'         => 'open',
            'last_message_at' => now(),
        ]);

        $this->actingAs($this->customer)
            ->get(route('customer.chat.messages', $conversation))
            ->assertForbidden();
    }

    public function test_chu_san_tra_loi_duoc_khach_cua_minh(): void
    {
        $this->actingAs($this->customer)->post(route('customer.chat.venue', $this->venue));
        $conversation = ChatConversation::first();

        $this->actingAs($this->owner)
            ->post(route('owner.chats.reply', $conversation), ['message' => 'Sân còn trống nhé bạn'])
            ->assertOk();

        $reply = ChatMessage::where('sender_type', 'owner')->latest('id')->first();
        $this->assertEquals('Sân còn trống nhé bạn', $reply->message);
        $this->assertEquals($this->owner->id, $reply->sender_id);
    }

    public function test_chu_san_khong_tra_loi_duoc_hoi_thoai_ho_tro(): void
    {
        // Hội thoại hỗ trợ của khách (không thuộc chủ sân nào)
        $conversation = ChatConversation::create([
            'user_id'        => $this->customer->id,
            'type'           => 'support',
            'customer_name'  => $this->customer->name,
            'status'         => 'open',
            'last_message_at' => now(),
        ]);

        $this->actingAs($this->owner)
            ->post(route('owner.chats.reply', $conversation), ['message' => 'x'])
            ->assertNotFound();
    }

    public function test_chu_san_chi_thay_hoi_thoai_cua_khu_san_minh(): void
    {
        $venueConversation = ChatConversation::create([
            'user_id'         => $this->customer->id,
            'type'            => 'owner',
            'venue_id'        => $this->venue->id,
            'owner_id'        => $this->owner->id,
            'customer_name'   => $this->customer->name,
            'status'          => 'open',
            'last_message_at' => now(),
        ]);

        // Hội thoại owner của chủ sân khác — không được lộ ra
        $otherOwner = User::factory()->create(['role' => 'owner', 'email_verified_at' => now()]);
        ChatConversation::create([
            'user_id'         => $this->customer->id,
            'type'            => 'owner',
            'venue_id'        => $this->venue->id,
            'owner_id'        => $otherOwner->id,
            'customer_name'   => $this->customer->name,
            'status'          => 'open',
            'last_message_at' => now(),
        ]);

        $response = $this->actingAs($this->owner)->get(route('owner.chats.index'));
        $response->assertOk();

        $visible = $response->viewData('conversations');
        $this->assertEquals(1, $visible->total());
        $this->assertEquals($venueConversation->id, $visible->first()->id);
    }

    public function test_admin_chi_thay_hoi_thoai_ho_tro(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'email_verified_at' => now()]);

        ChatConversation::create([
            'user_id'         => $this->customer->id,
            'type'            => 'support',
            'customer_name'   => $this->customer->name,
            'status'          => 'open',
            'last_message_at' => now(),
        ]);
        ChatConversation::create([
            'user_id'         => $this->customer->id,
            'type'            => 'owner',
            'venue_id'        => $this->venue->id,
            'owner_id'        => $this->owner->id,
            'customer_name'   => $this->customer->name,
            'status'          => 'open',
            'last_message_at' => now(),
        ]);

        $response = $this->actingAs($admin)->get(route('admin.chats.index'));
        $response->assertOk();

        $this->assertEquals(1, $response->viewData('conversations')->total());
    }
}
