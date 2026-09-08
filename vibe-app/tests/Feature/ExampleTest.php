<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     */
    public function test_the_homepage_returns_the_generation_form(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200)
            ->assertSee('business_type')
            ->assertSee('Generate');
    }

    public function test_homepage_has_a_contact_feedback_button(): void
    {
        $this->get('/')->assertOk()->assertSee('CONTACT / FEEDBACK')->assertSee('contact');
    }

    public function test_generate_route_returns_dummy_response(): void
    {
        $response = $this->post('/generate', [
            'business_type' => 'coffee shop',
        ]);

        $response->assertStatus(200)
            ->assertSee('Generated successfully');
    }

    public function test_shop_page_returns_catalog(): void
    {
        $response = $this->get('/shop');

        $response->assertStatus(200)
            ->assertSee('ARCHIVAL COLLECTION / SS25')
            ->assertSee('SHOP ALL');
    }

    public function test_lookbook_page_returns_the_ss25_editorial(): void
    {
        $this->get('/lookbook-ss25')
            ->assertOk()
            ->assertSee('LOOKBOOK SS25')
            ->assertSee('ARCHITECTURAL FORM')
            ->assertSee('THE MONOLITHIC');
    }

    public function test_contact_page_sends_a_concierge_ticket(): void
    {
        $this->get('/contact')
            ->assertOk()
            ->assertSee('CLIENT CARE')
            ->assertSee('TRANSMIT INQUIRY TO ATELIER');

        $response = $this->post('/contact', [
            'specialization' => 'feedback',
            'name' => 'Alyssa Reyes',
            'contact' => 'alyssa@example.com',
            'channel' => 'email',
            'message' => 'The SS25 archive presentation was excellent.',
        ]);

        $response->assertRedirect(route('contact'));
        $this->followRedirects($response)->assertSee('Ticket reference logged');
        $this->assertStringStartsWith('MNL-SR-', session('last_support_ticket.reference'));
    }

    public function test_products_can_be_added_to_the_session_cart(): void
    {
        $response = $this->postJson('/cart/add', [
            'slug' => 'k-01-structural-boxy-tee',
            'name' => 'K-01 STRUCTURAL BOXY TEE',
            'price' => '₱2,250.00',
            'image' => 'https://example.com/product.jpg',
        ]);

        $response->assertOk()->assertJsonPath('redirect', route('cart'));

        $this->get('/cart')
            ->assertOk()
            ->assertSee('K-01 STRUCTURAL BOXY TEE')
            ->assertSee('₱2,250.00')
            ->assertSee('PROCEED TO CHECKOUT')
            ->assertSee(route('checkout'));
    }

    public function test_checkout_page_shows_the_session_cart(): void
    {
        $this->withSession([
            'customer_login' => ['email' => 'client@example.com'],
            'cart' => [
            'k-01-structural-boxy-tee' => [
                'slug' => 'k-01-structural-boxy-tee',
                'name' => 'K-01 STRUCTURAL BOXY TEE',
                'price' => '₱2,250.00',
                'image' => 'https://example.com/product.jpg',
                'quantity' => 1,
            ],
        ]])->get('/checkout')
            ->assertOk()
            ->assertSee('CHECKOUT')
            ->assertSee('K-01 STRUCTURAL BOXY TEE')
            ->assertSee('PLACE ORDER');
    }

    public function test_customer_dashboard_shows_account_and_cart_summary(): void
    {
        $this->withSession([
            'customer_login' => ['email' => 'client@example.com'],
            'cart' => [
            'k-01-structural-boxy-tee' => [
                'slug' => 'k-01-structural-boxy-tee',
                'name' => 'K-01 STRUCTURAL BOXY TEE',
                'price' => '₱2,250.00',
                'image' => 'https://example.com/product.jpg',
                'quantity' => 1,
            ],
        ]])->get('/dashboard')
            ->assertOk()
            ->assertSee('CUSTOMER PORTAL')
            ->assertSee('K-01 STRUCTURAL BOXY TEE')
            ->assertSee('PROCEED TO CHECKOUT')
            ->assertSee('ACCOUNT STATUS')
            ->assertSee('CONTACT / FEEDBACK');
    }

    public function test_customer_dashboard_redirects_guests_to_customer_login(): void
    {
        $this->get('/dashboard')->assertRedirect(route('login'));
    }

    public function test_customer_can_log_out_from_the_dashboard(): void
    {
        $this->withSession(['customer_login' => ['email' => 'client@example.com']])
            ->post('/logout')
            ->assertRedirect(route('login'));

        $this->get('/dashboard')->assertRedirect(route('login'));
    }

    public function test_customer_login_shows_the_client_access_form_and_enters_dashboard(): void
    {
        $this->get('/login')
            ->assertOk()
            ->assertSee('CLIENT ACCESS')
            ->assertSee('AUTHENTICATE')
            ->assertSee('CREATE ACCOUNT')
            ->assertSee(route('register'));

        $this->withSession(['customer_accounts' => [
            'client@example.com' => ['password' => Hash::make('password123')],
        ]])->post('/login', [
            'email' => 'client@example.com',
            'password' => 'password123',
            'remember' => '1',
        ])->assertRedirect(route('dashboard'));

        $this->assertSame('client@example.com', session('customer_login.email'));
    }

    public function test_customer_registration_creates_a_client_portfolio_session(): void
    {
        $this->get('/register')
            ->assertOk()
            ->assertSee('CLIENT PORTFOLIO CREATION')
            ->assertSee('CREATE ARCHIVE ACCOUNT');

        $response = $this->post('/register', [
            'first_name' => 'Alyssa Marie',
            'last_name' => 'Reyes',
            'phone' => '9178881920',
            'email' => 'alyssa@example.com',
            'password' => 'password123',
            'welcome_credit' => '1',
            'drop_alerts' => '1',
            'terms' => '1',
        ])->assertRedirect(route('login'));

        $this->assertSame('Alyssa Marie', session('customer_profile.first_name'));
        $this->assertFalse(session()->has('customer_login'));
        $this->followRedirects($response)->assertSee('Account created successfully');
    }

    public function test_admin_dashboard_returns_operations_overview(): void
    {
        $this->withSession(['admin_login' => ['operator_id' => 'pryvstpedrera@gmail.com']])->get('/admin/dashboard')
            ->assertOk()
            ->assertSee('OPERATIONAL COMMAND CENTER')
            ->assertSee('Gross Revenue MTD')
            ->assertSee('Active Waybills')
            ->assertSee('Vault Re-Stock');
    }

    public function test_admin_inventory_page_is_available(): void
    {
        $this->withSession(['admin_login' => ['operator_id' => 'pryvstpedrera@gmail.com']])->get('/admin/inventory')
            ->assertOk()
            ->assertSee('Product Inventory')
            ->assertSee('ADD PRODUCT')
            ->assertSee('K-01 Structural Boxy Tee');

        $this->withSession(['admin_login' => ['operator_id' => 'pryvstpedrera@gmail.com']])->post('/admin/inventory/k-01-structural-boxy-tee', [
            'name' => 'K-01 Updated Boxy Tee',
            'price' => '2495',
            'stock' => '12',
            'low_stock_threshold' => '4',
        ])->assertRedirect(route('admin.inventory'));

        $this->withSession(['admin_login' => ['operator_id' => 'pryvstpedrera@gmail.com']])->get('/admin/inventory')->assertSee('K-01 Updated Boxy Tee');
        $this->get('/')->assertSee('K-01 Updated Boxy Tee')->assertSee('2495');
        $this->get('/shop')->assertSee('K-01 Updated Boxy Tee')->assertSee('2495');

        $this->withSession(['admin_login' => ['operator_id' => 'pryvstpedrera@gmail.com']])->delete('/admin/inventory/k-01-structural-boxy-tee')
            ->assertRedirect(route('admin.inventory'));

        $this->withSession(['admin_login' => ['operator_id' => 'pryvstpedrera@gmail.com']])->get('/admin/inventory')->assertDontSee('K-01 Updated Boxy Tee');
    }

    public function test_admin_pages_redirect_guests_to_the_homepage(): void
    {
        $this->get('/admin/dashboard')->assertRedirect(route('home'));
        $this->get('/admin/inventory')->assertRedirect(route('home'));
    }

    public function test_admin_operation_sections_are_available_to_authenticated_admins(): void
    {
        $session = ['admin_login' => ['operator_id' => 'pryvstpedrera@gmail.com']];

        foreach (['orders', 'clientele', 'financials', 'settings'] as $section) {
            $this->withSession($session)->get('/admin/' . $section)->assertOk();
        }
    }

    public function test_admin_can_log_out_from_the_system_settings(): void
    {
        $this->withSession(['admin_login' => ['operator_id' => 'pryvstpedrera@gmail.com']])
            ->post('/admin/logout')
            ->assertRedirect(route('admin.login'));

        $this->get('/admin/dashboard')->assertRedirect(route('home'));
    }

    public function test_admin_login_opens_an_atelier_operations_session(): void
    {
        $this->get('/admin/login')
            ->assertOk()
            ->assertSee('CONTROL ROOM AUTHENTICATION')
            ->assertSee('AUTHENTICATE');

        $this->post('/admin/login', [
            'operator_id' => 'pryvstpedrera@gmail.com',
            'passkey' => 'Kruzo0530',
            'division' => 'dispatch',
            'trusted_session' => '1',
        ])->assertRedirect(route('admin.dashboard'));

        $this->assertSame('pryvstpedrera@gmail.com', session('admin_login.operator_id'));
    }

    public function test_successful_checkout_redirects_to_thank_you_page_and_clears_cart(): void
    {
        $response = $this->withSession(['cart' => [
            'k-01-structural-boxy-tee' => [
                'slug' => 'k-01-structural-boxy-tee',
                'name' => 'K-01 STRUCTURAL BOXY TEE',
                'price' => '₱2,250.00',
                'image' => 'https://example.com/product.jpg',
                'quantity' => 1,
            ],
        ]])->post('/checkout', [
            'email' => 'customer@example.com',
            'first_name' => 'Alyssa',
            'last_name' => 'Reyes',
            'password' => 'secure-password',
            'password_confirmation' => 'secure-password',
            'address' => 'Unit 24B, Tower 2',
            'barangay' => 'Fort Bonifacio',
            'city' => 'Taguig',
            'province' => 'Metro Manila',
            'postal_code' => '1634',
            'phone' => '+63 917 842 5591',
            'payment_method' => 'cod',
        ]);

        $response->assertRedirect(route('thank-you'));

        $this->followRedirects($response)
            ->assertOk()
            ->assertSee('ORDER CONFIRMED')
            ->assertSee('KRZ-MNL-')
            ->assertSee('K-01 STRUCTURAL BOXY TEE')
            ->assertSee('Alyssa Reyes');

        $this->assertSame([], session('cart', []));

        $this->get('/dashboard')
            ->assertOk()
            ->assertSee('ORDER ITEMS')
            ->assertSee('K-01 STRUCTURAL BOXY TEE');

        $this->withSession(['admin_login' => ['operator_id' => 'pryvstpedrera@gmail.com']])->get('/admin/dashboard')
            ->assertOk()
            ->assertSee('KRZ-MNL-')
            ->assertSee('Alyssa Reyes');

        $this->withSession(['admin_login' => ['operator_id' => 'pryvstpedrera@gmail.com']])->post('/admin/orders/' . session('last_order.number') . '/status', [
            'status' => 'shipped',
        ])->assertRedirect(route('admin.dashboard'));

        $this->withSession(['admin_login' => ['operator_id' => 'pryvstpedrera@gmail.com']])->get('/admin/dashboard')
            ->assertOk()
            ->assertSee('SHIPPED');

        $this->get('/dashboard')
            ->assertOk()
            ->assertSee('LIVE ORDER STATUS')
            ->assertSee('SHIPPED');
    }

    public function test_each_catalog_product_has_a_product_page(): void
    {
        foreach ([
            'k-01-structural-boxy-tee',
            'brutalist-monolith-cuff-ring-set',
            'k-02-dropped-raglan-longline',
            'geometric-carabiner-key-tether',
            'k-03-wide-leg-pleated-cargo',
            'atelier-heavyweight-tank',
            'modular-crossbody-chest-rig',
            'k-04-sculpted-oversized-hoodie',
        ] as $slug) {
            $this->get('/product/' . $slug)->assertOk();
        }
    }
}
