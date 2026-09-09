<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\InventoryItem;
use App\Models\User;
use App\Models\SupportMessage;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(Request $request): View
    {
        return view('home', ['inventory' => $this->catalogInventory($request)]);
    }

    public function shop(Request $request): View
    {
        return view('shop', [
            'inventory' => $this->catalogInventory($request),
            'newProducts' => [
                ['slug' => 'k-05-structural-raglan-crew', 'name' => 'K-05 STRUCTURAL RAGLAN CREW', 'price' => 3250, 'category' => 'heavyweight', 'gsm' => 420, 'image' => asset('images/products/k-05-structural-raglan-crew.png'), 'material' => 'HEAVYWEIGHT COTTON FLEECE'],
                ['slug' => 'k-06-washed-cargo-tee', 'name' => 'K-06 WASHED CARGO TEE', 'price' => 2450, 'category' => 'oversized', 'gsm' => 280, 'image' => asset('images/products/k-06-washed-cargo-tee.png'), 'material' => '280 GSM WASHED JERSEY'],
                ['slug' => 'k-07-raw-cut-box-tee', 'name' => 'K-07 RAW CUT BOX TEE', 'price' => 2250, 'category' => 'oversized', 'gsm' => 280, 'image' => asset('images/products/k-07-raw-cut-box-tee.png'), 'material' => 'DENSE COMBED COTTON'],
                ['slug' => 'k-08-sculpted-concrete-hoodie', 'name' => 'K-08 SCULPTED CONCRETE HOODIE', 'price' => 3950, 'category' => 'heavyweight', 'gsm' => 450, 'image' => asset('images/products/k-08-sculpted-concrete-hoodie.png'), 'material' => '450 GSM FRENCH TERRY'],
                ['slug' => 'k-09-modular-field-cargo', 'name' => 'K-09 MODULAR FIELD CARGO', 'price' => 3850, 'category' => 'trousers', 'gsm' => 320, 'image' => asset('images/products/k-09-modular-field-cargo.png'), 'material' => 'DURABLE COTTON RIPSTOP'],
                ['slug' => 'k-10-wide-pleat-trouser', 'name' => 'K-10 WIDE PLEAT TROUSER', 'price' => 3650, 'category' => 'trousers', 'gsm' => 280, 'image' => asset('images/products/k-10-wide-pleat-trouser.png'), 'material' => 'TECHNICAL SUITING CLOTH'],
                ['slug' => 'k-11-modular-chest-harness', 'name' => 'K-11 MODULAR CHEST HARNESS', 'price' => 2950, 'category' => 'accessories', 'gsm' => 240, 'image' => asset('images/products/k-11-modular-chest-harness.png'), 'material' => 'TECHNICAL BALLISTIC NYLON'],
            ],
        ]);
    }

    public function lookbook(): View
    {
        return view('lookbook');
    }

    public function wishlist(Request $request): View
    {
        return view('wishlist', ['inventory' => $this->catalogInventory($request)]);
    }

    public function contact(): View
    {
        return view('contact');
    }

    public function sendContact(Request $request)
    {
        $data = $request->validate([
            'specialization' => ['required', 'in:retail,dispatch,recalibration,provenance,feedback'],
            'name' => ['required', 'string', 'max:160'],
            'contact' => ['required', 'string', 'max:255'],
            'channel' => ['required', 'in:viber,sms,email,phone'],
            'message' => ['required', 'string', 'max:3000'],
        ]);

        $ticket = 'MNL-SR-' . now()->format('ymdHis');
        $record = [...$data, 'reference' => $ticket];
        $messages = $this->readSupportMessages();
        $messages[] = [...$record, 'created_at' => now()->toDateTimeString()];
        Storage::disk('local')->put('support_messages.json', json_encode($messages, JSON_PRETTY_PRINT));
        $request->session()->put('support_messages', $messages);

        if ($this->databaseIsConfigured()) {
            try {
                SupportMessage::create($record);
            } catch (QueryException $exception) {
                report($exception);
            }
        }

        $request->session()->put('last_support_ticket', $record);

        return redirect()->route('contact')->with('contact_success', $ticket);
    }

    public function product(string $slug): View
    {
        $products = [
            'k-01-structural-boxy-tee' => ['name' => 'K-01 STRUCTURAL BOXY TEE', 'price' => '₱2,250.00', 'image' => 'https://lh3.googleusercontent.com/aida/AEtjO1Wz-ia6VtilLPVNQxEr1QbmA_k7DrxJYlOQR_VkkL48aTCfsd-wmzoBO6UvW-j31_maSAVJ9y9gSN3QfrbcCtUQx729zbaHKs46FO9IzkTlMeogDQfkUFjYw83w1PZNpphaRlBpXOpC4m1zjNv3yYb6k6oXGgNTZpxp2btSXL1FKIO1_hUDaWzWoPuSEjUFLNXH1WujtXSzt4ZWyosRg8qQbKWpSzLr9DmcDcUBFmI_IilzvPLT1Lw6RA'],
            'brutalist-monolith-cuff-ring-set' => ['name' => 'BRUTALIST MONOLITH BEVELED CUFF & RING SET', 'price' => '₱3,450.00', 'image' => 'https://lh3.googleusercontent.com/aida/AEtjO1XjsY_g2LeitItel0xsUmMx3sRmepf1byvHozGrNKArO11EGRzUxEFtFjYx2piS8ptMPG6KEla2PcBIwg5Q9jrP3RbmQDuUDTYk4N2qh1M7X3NUhgoqPOoPzlroKGIAHnPknCaxm3VP1QK_KtZWn3QED7crBg54GxpQ64AtU3qRz2VLKAo5C-EDmn8zGQ-eJFVGLA0Oq4kqP6QqFVnAWjKWj-yF4qY3rIMcT2yX0YX9WYl3xSfL15UQVXI'],
            'k-02-dropped-raglan-longline' => ['name' => 'K-02 DROPPED RAGLAN LONGLINE', 'price' => '₱2,650.00', 'image' => 'https://lh3.googleusercontent.com/aida-public/AB6AXuCa1nSLp_1Ej941BYeLlk7ynCQpe0cxkbBw50h6A8u1ZrkzjRGoF-3GeePTU2d_LqhrelxnpWCqoaGdTZhs4bsbpQsCrVcYBSt5rdGldx0jDfxlUahRFTRFK8isTezobdIDJse4XXmXrLD_yPVqb56OgxNic-oXHd45C-jOavPANl63M5ISiA_tUXdv8brV71saJn6Uwt1PEYW6p4ctI_CHzHvTefEbdArH-ClM3QXiJATSgIYNAYg-'],
            'geometric-carabiner-key-tether' => ['name' => 'GEOMETRIC CARABINER & MODULAR KEY TETHER', 'price' => '₱1,850.00', 'image' => 'https://lh3.googleusercontent.com/aida-public/AB6AXuB4Az7uS4TYYtPt4MGM-wwPs_ADiE3DeQ0BoXJvm_gnE9z_u8hkWoN2k3GZSb3pX3ez7hHnkPS9D4L4fLtW4AYiwMELDpLu1FSMr65d7ZcToR1HnbosUr7j8DyzKHL6oNhMjKk4lymRls8vhDauv22kvazMtmonyuoqaSpD6ZcXIs0esyOSUgmQjmPbmDM3qZj6UYp52GJZsJxBHMoGZzfTzbRfE_PBWNNzyKvZC1_H0ZXE3iAOSqXp'],
            'k-03-wide-leg-pleated-cargo' => ['name' => 'K-03 ARCHITECTURAL WIDE-LEG PLEATED CARGO', 'price' => '₱3,850.00', 'image' => 'https://lh3.googleusercontent.com/aida-public/AB6AXuBecqRdCM_iwegB1AgQI8t1awkABBEtGBu5LiGJ6P7qYYT3FnE5Vh7XNHCh9__qdNxK6aIlk6n3ynY4LaWBxyrK69qXE7nUIg0AlJhhOxGxsLMk2giGYPZb8YhkcOYe8Cs7z8d_bOVQz4JdkJxvNvlkKtQFogzNK_CeyiKeqJseSpslwscUnOSVuO_GbKHZn4BGtZbLNlaRjS8an9Ji4bw0Yh5T_7skQd_5BeoQpOfAZwaOpv6oRiB3'],
            'atelier-heavyweight-tank' => ['name' => 'ATELIER HEAVYWEIGHT TANK', 'price' => '₱1,650.00', 'image' => 'https://lh3.googleusercontent.com/aida-public/AB6AXuCfL4_b0eYKwjpS2DVwMiqBLXMO9flsW3yj1FRLzKnpDR-sJCz8e8Jw4PytjuXm2lv-mubyWQ5GUPxZIxXUeHK18jiUDEkqP49o6GVG5jX8BOebI3ZlRz4VDASdzv8t7nSGmL5iQSc0R7Q0ea10OwlhUojmMKfmuuhw2Vo4Ks7RLsxmAD9PaImDQVlzX1d5bygvT1xfXhhX3zSA4UJceeRF9EKaJZrhFG_ZiXN94VgCtrNh2QtZdVOl'],
            'modular-crossbody-chest-rig' => ['name' => 'MODULAR CROSSBODY CHEST RIG RIGID POUCH', 'price' => '₱2,950.00', 'image' => 'https://lh3.googleusercontent.com/aida-public/AB6AXuBXUV29PU5blJ4q3aNBMARwF-qbP2NsBrJCVg4CNxT2Frjg3upls02wM-wJAZz9IUqCMzNORrW5xihXKMeHr4kE9Q60iEy9eRHWpNyS7SjYJJ_NSSuEmgLBEwlmw9yRkFVwyP1kR5AO8qAq0WA7iCIj4GIJaj5sCpSxauWGMnZg6sYdNTrq0OcVBoF_rrYYd2a28AfhXXlBvI1W6o0h2tu8lofByYpcSjaBkMQaZmnWO_jGR7XIZFUt'],
            'k-04-sculpted-oversized-hoodie' => ['name' => 'K-04 SCULPTED OVERSIZED HOODIE', 'price' => '₱3,650.00', 'image' => 'https://lh3.googleusercontent.com/aida-public/AB6AXuAZVy9PAagR9l8A-6kWLFH0eNT6p1TNDhTJm4ItJsE8VLGnBgpEQK8tL-IuYsmOUw0xtxCSi9yYKfezRPvkj2D_C5cmyYDcdMlkPzo4hzdlIKO1aDL1e23eFHeNiCp5Rd4H6ghnSImGVt5Edgkz6DFrSirg6SZCpQMaP2M2V36OJ5RHAXbJUFH6qPNR5p2BU9fXlBFElwTKxq90uSVn0yZb3nK4voACFh05QILktebBgQiWeCYW1Ad7'],
            'k-05-structural-raglan-crew' => ['name' => 'K-05 STRUCTURAL RAGLAN CREW', 'price' => '₱3,250.00', 'image' => asset('images/products/k-05-structural-raglan-crew.png')],
            'k-06-washed-cargo-tee' => ['name' => 'K-06 WASHED CARGO TEE', 'price' => '₱2,450.00', 'image' => asset('images/products/k-06-washed-cargo-tee.png')],
            'k-07-raw-cut-box-tee' => ['name' => 'K-07 RAW CUT BOX TEE', 'price' => '₱2,250.00', 'image' => asset('images/products/k-07-raw-cut-box-tee.png')],
            'k-08-sculpted-concrete-hoodie' => ['name' => 'K-08 SCULPTED CONCRETE HOODIE', 'price' => '₱3,950.00', 'image' => asset('images/products/k-08-sculpted-concrete-hoodie.png')],
            'k-09-modular-field-cargo' => ['name' => 'K-09 MODULAR FIELD CARGO', 'price' => '₱3,850.00', 'image' => asset('images/products/k-09-modular-field-cargo.png')],
            'k-10-wide-pleat-trouser' => ['name' => 'K-10 WIDE PLEAT TROUSER', 'price' => '₱3,650.00', 'image' => asset('images/products/k-10-wide-pleat-trouser.png')],
            'k-11-modular-chest-harness' => ['name' => 'K-11 MODULAR CHEST HARNESS', 'price' => '₱2,950.00', 'image' => asset('images/products/k-11-modular-chest-harness.png')],
        ];

        // Keep product pages independent of expired remote image URLs.
        $localProductImages = [
            'k-01-structural-boxy-tee' => 'images/products/k-07-raw-cut-box-tee.png',
            'brutalist-monolith-cuff-ring-set' => 'images/products/k-11-modular-chest-harness.png',
            'k-02-dropped-raglan-longline' => 'images/products/k-06-washed-cargo-tee.png',
            'geometric-carabiner-key-tether' => 'images/products/k-11-modular-chest-harness.png',
            'k-03-wide-leg-pleated-cargo' => 'images/products/k-09-modular-field-cargo.png',
            'atelier-heavyweight-tank' => 'images/products/k-06-washed-cargo-tee.png',
            'modular-crossbody-chest-rig' => 'images/products/k-11-modular-chest-harness.png',
            'k-04-sculpted-oversized-hoodie' => 'images/products/k-08-sculpted-concrete-hoodie.png',
        ];
        foreach ($localProductImages as $productSlug => $imagePath) {
            if (isset($products[$productSlug])) {
                $products[$productSlug]['image'] = asset($imagePath);
            }
        }

        $productDetails = [
            'k-01-structural-boxy-tee' => [
                'description' => 'The K-01 is a structured boxy tee engineered for the modern Manila wardrobe. Its drop shoulder and clean volume hold their shape through daily movement and tropical humidity.',
                'materials' => ['280 GSM compact combed cotton', 'High-density ribbed cotton collar', 'Pre-washed Washed Onyx finish', 'Double-needle stitched hems'],
                'sizing' => 'True to the oversized boxy fit. Choose your standard size for the signature silhouette, or size down for a neater fit.',
            ],
            'brutalist-monolith-cuff-ring-set' => [
                'description' => 'A modular architectural accessory set with a solid industrial presence. The cuff and ring are designed to layer cleanly with the SS25 collection.',
                'materials' => ['316L surgical-grade stainless steel', 'Hand-buffed matte finish', 'Laser-etched atelier serial detail', 'Anti-tarnish protective treatment'],
                'sizing' => 'The cuff is OS adjustable. Select Ring 08 or Ring 10 for the preferred ring fit.',
            ],
            'k-02-dropped-raglan-longline' => [
                'description' => 'The K-02 extends the everyday tee into a longer architectural line, with a dropped raglan shoulder and controlled rear drape for effortless movement.',
                'materials' => ['320 GSM French terry cotton blend', 'Double-needle lock stitching', 'Reinforced ribbed neck binding', 'Concrete Heather garment-dyed finish'],
                'sizing' => 'Select your standard size for a relaxed longline fit. Size down for a closer shoulder and shorter drape.',
            ],
            'geometric-carabiner-key-tether' => [
                'description' => 'A compact EDC tether built around modular hardware and fast access. Designed to secure daily essentials without adding visual bulk.',
                'materials' => ['Grade 5 aerospace titanium carabiner', 'Anodized matte onyx hardware', 'Mil-spec high-density webbing', 'Magnetic quick-release connector'],
                'sizing' => 'One size. The modular tether adjusts to belts, bags, and standard attachment points.',
            ],
            'k-03-wide-leg-pleated-cargo' => [
                'description' => 'The K-03 is a wide-leg trouser with architectural pleats and utility pockets. Its considered volume creates a strong line while staying easy to move in.',
                'materials' => ['Structured cotton twill', 'Durable utility pocket lining', 'Reinforced bartack stress points', 'Pre-washed graphite finish'],
                'sizing' => 'Designed to sit at the natural waist with a wide leg. Choose your usual waist size; the pleats provide additional ease through the hip.',
            ],
            'atelier-heavyweight-tank' => [
                'description' => 'A heavyweight foundation layer cut with a clean architectural neckline. Wear it alone in the heat or under the collection’s outer silhouettes.',
                'materials' => ['420 GSM heavyweight cotton jersey', 'Dense reinforced binding', 'Pre-shrunk construction', 'Soft-washed charcoal finish'],
                'sizing' => 'Relaxed through the body. Choose your usual size for a substantial fit or size down for a sharper base layer.',
            ],
            'modular-crossbody-chest-rig' => [
                'description' => 'A rigid modular chest rig for hands-free carry. The compact profile keeps essentials close while the structured panels preserve the KRUZO silhouette.',
                'materials' => ['Technical ballistic nylon shell', 'Rigid molded utility panels', 'Industrial webbing straps', 'Matte black magnetic hardware'],
                'sizing' => 'One size with adjustable crossbody and chest straps. Fit it high across the torso for the intended profile.',
            ],
            'k-04-sculpted-oversized-hoodie' => [
                'description' => 'The K-04 is a sculpted oversized hoodie with a substantial hand feel and precise volume. Built for cooler evenings, studio sessions, and layered city movement.',
                'materials' => ['450 GSM French terry cotton', 'Double-layer hood', 'Reinforced rib cuffs and hem', 'Brushed interior with washed concrete finish'],
                'sizing' => 'Intentionally oversized. Choose your usual size for the full sculpted volume or size down for a more controlled fit.',
            ],
            'k-05-structural-raglan-crew' => [
                'description' => 'A clean raglan crew engineered for transitional weather, with a relaxed architectural body and soft concrete-grey finish.',
                'materials' => ['Heavyweight cotton fleece', 'Raglan sleeve construction', 'Reinforced rib collar and cuffs', 'Pre-washed concrete grey finish'],
                'sizing' => 'Relaxed fit. Choose your standard size for a measured layer or size up for a fuller outer layer.',
            ],
            'k-06-washed-cargo-tee' => [
                'description' => 'An oversized washed tee with utility-minded proportions and a substantial hand feel for everyday urban wear.',
                'materials' => ['280 GSM compact cotton jersey', 'Garment-washed charcoal finish', 'Dropped shoulder seams', 'Reinforced sleeve and hem stitching'],
                'sizing' => 'Oversized through the body. Choose your usual size for the intended fit.',
            ],
            'k-07-raw-cut-box-tee' => [
                'description' => 'A raw-edged box tee that balances a cropped hem with broad shoulders and an easy architectural silhouette.',
                'materials' => ['Dense combed cotton jersey', 'Raw-cut hem treatment', 'Drop shoulder pattern', 'Pre-shrunk off-white finish'],
                'sizing' => 'Cropped and wide. Choose your usual size; size up for additional length and volume.',
            ],
            'k-08-sculpted-concrete-hoodie' => [
                'description' => 'A sculpted hoodie with exaggerated sleeves, a structured hood, and a compact profile made for cool studio nights.',
                'materials' => ['450 GSM French terry cotton', 'Double-layer sculpted hood', 'Reinforced kangaroo pocket', 'Heavy rib cuff and hem binding'],
                'sizing' => 'Intentionally oversized. Size down for a closer silhouette.',
            ],
            'k-09-modular-field-cargo' => [
                'description' => 'A modular cargo trouser built around practical storage and a technical field silhouette, finished with adjustable ankle volume.',
                'materials' => ['Durable cotton ripstop', 'Multi-pocket modular panel system', 'Reinforced bartack stress points', 'Adjustable drawcord hem'],
                'sizing' => 'Relaxed utility fit. Choose your usual waist size for room through the seat and thigh.',
            ],
            'k-10-wide-pleat-trouser' => [
                'description' => 'A wide pleat trouser with a clean tailored line, balanced by a relaxed leg and architectural drape.',
                'materials' => ['Structured technical suiting cloth', 'Double front pleat', 'Reinforced waistband', 'Soft matte black finish'],
                'sizing' => 'High-waisted with a wide leg. Choose your usual waist size for the intended drape.',
            ],
            'k-11-modular-chest-harness' => [
                'description' => 'A compact modular chest harness that brings utility storage into the KRUZO silhouette without sacrificing a clean profile.',
                'materials' => ['Technical ballistic nylon', 'Molded utility pouch', 'Adjustable webbing harness', 'Matte black quick-release hardware'],
                'sizing' => 'One size with adjustable shoulder and torso straps. Wear close to the chest for a stable fit.',
            ],
        ];

        abort_unless(isset($products[$slug]), 404);

        $products[$slug] = [...$products[$slug], ...$productDetails[$slug]];

        return view('product', [
            'product' => $products[$slug],
            'slug' => $slug,
            'reviews' => $this->readLocalReviews()[$slug] ?? [],
        ]);
    }

    public function submitReview(Request $request, string $slug)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'rating' => ['required', 'integer', 'between:1,5'],
            'title' => ['required', 'string', 'max:160'],
            'body' => ['required', 'string', 'max:2000'],
        ]);

        $reviews = $this->readLocalReviews();
        $reviews[$slug] = [
            ...($reviews[$slug] ?? []),
            [...$data, 'created_at' => now()->toDateTimeString()],
        ];
        $this->writeLocalReviews($reviews);

        return redirect()->route('product', $slug)->with('review_success', 'Your review has been published.');
    }

    private function databaseIsConfigured(): bool
    {
        if (app()->environment('testing')) {
            return false;
        }

        $connection = (string) config('database.default');

        if ($connection === '' || $connection === 'sqlite') {
            return false;
        }

        $database = (string) config("database.connections.{$connection}.database");
        $username = (string) config("database.connections.{$connection}.username");

        return $database !== ''
            && $username !== ''
            && ! str_contains(strtolower($database), 'your_database')
            && ! str_contains(strtolower($username), 'your_database');
    }

    private function localInventory(): array
    {
        return [
            ['sku' => 'k-01-structural-boxy-tee', 'name' => 'K-01 Structural Boxy Tee', 'stock' => 18, 'low_stock_threshold' => 5, 'price' => 2250],
            ['sku' => 'brutalist-monolith-cuff-ring-set', 'name' => 'Brutalist Monolith Cuff Ring Set', 'stock' => 14, 'low_stock_threshold' => 5, 'price' => 3450],
            ['sku' => 'k-02-dropped-raglan-longline', 'name' => 'K-02 Dropped Raglan Longline', 'stock' => 12, 'low_stock_threshold' => 5, 'price' => 2650],
            ['sku' => 'geometric-carabiner-key-tether', 'name' => 'Geometric Carabiner Key Tether', 'stock' => 24, 'low_stock_threshold' => 6, 'price' => 1850],
            ['sku' => 'k-03-wide-leg-pleated-cargo', 'name' => 'K-03 Wide Leg Pleated Cargo', 'stock' => 10, 'low_stock_threshold' => 4, 'price' => 3850],
            ['sku' => 'atelier-heavyweight-tank', 'name' => 'Atelier Heavyweight Tank', 'stock' => 20, 'low_stock_threshold' => 5, 'price' => 1650],
            ['sku' => 'modular-crossbody-chest-rig', 'name' => 'Modular Crossbody Chest Rig', 'stock' => 9, 'low_stock_threshold' => 3, 'price' => 2950],
            ['sku' => 'k-04-sculpted-oversized-hoodie', 'name' => 'K-04 Sculpted Oversized Hoodie', 'stock' => 7, 'low_stock_threshold' => 3, 'price' => 3650],
            ['sku' => 'k-05-structural-raglan-crew', 'name' => 'K-05 Structural Raglan Crew', 'stock' => 15, 'low_stock_threshold' => 5, 'price' => 3250],
            ['sku' => 'k-06-washed-cargo-tee', 'name' => 'K-06 Washed Cargo Tee', 'stock' => 16, 'low_stock_threshold' => 5, 'price' => 2450],
            ['sku' => 'k-07-raw-cut-box-tee', 'name' => 'K-07 Raw Cut Box Tee', 'stock' => 8, 'low_stock_threshold' => 3, 'price' => 2250],
            ['sku' => 'k-08-sculpted-concrete-hoodie', 'name' => 'K-08 Sculpted Concrete Hoodie', 'stock' => 6, 'low_stock_threshold' => 3, 'price' => 3950],
            ['sku' => 'k-09-modular-field-cargo', 'name' => 'K-09 Modular Field Cargo', 'stock' => 11, 'low_stock_threshold' => 4, 'price' => 3850],
            ['sku' => 'k-10-wide-pleat-trouser', 'name' => 'K-10 Wide Pleat Trouser', 'stock' => 10, 'low_stock_threshold' => 4, 'price' => 3650],
            ['sku' => 'k-11-modular-chest-harness', 'name' => 'K-11 Modular Chest Harness', 'stock' => 5, 'low_stock_threshold' => 2, 'price' => 2950],
        ];
    }

    private function catalogInventory(Request $request): array
    {
        $items = collect($request->session()->get('admin_inventory', $this->localInventory()))
            ->map(fn (array $item) => [...$item, 'image' => $this->inventoryImageUrl($item['image'] ?? null)])
            ->all();

        if ($this->databaseIsConfigured()) {
            try {
                $items = InventoryItem::orderBy('name')->get()->map(fn (InventoryItem $item) => [
                    'sku' => $item->sku,
                    'name' => $item->name,
                    'stock' => $item->stock,
                    'low_stock_threshold' => $item->low_stock_threshold,
                    'price' => (float) $item->price,
                    'image' => $this->inventoryImageUrl($item->image),
                ])->all();
            } catch (QueryException $exception) {
                report($exception);
            }
        }

        $defaults = [
            'k-01-structural-boxy-tee' => ['category' => 'oversized', 'material' => '280 GSM COMBED COTTON', 'image' => 'https://lh3.googleusercontent.com/aida/AEtjO1V8Jmze18i8YzTS2QdfbXyxPSe27HTrU1l-yy_vCiBOzFouVvZJFDrrDtSSDHhKCaUSjurWXzh-GlhuYp0ZjOf6Tcs15vIZ3c7vrimP5ZlZqijIOTSTsSrH34vRHq0NRQ6z8UaEErzFShdj9Ovtjkya8nWqUkXzADoTzaHs9Lg8raquBjsO3F4JhHGpPgXA2zVvXx8kcoaBuzU8thywUrKR6HCjQCIHSirZwK2-PR4tkar590QRUjE0RA'],
            'brutalist-monolith-cuff-ring-set' => ['category' => 'accessories', 'material' => '316L SURGICAL STEEL', 'image' => 'https://lh3.googleusercontent.com/aida/AEtjO1Wy2MsFwkv4gtIKshaJywqsM5BGeDBoeLuR9cNjoPaorVANBq4_Qqw2dmVyGTybzbw_oBbxYt4WtfYkYGUTVO9OHFwFu5-bkzpqZhekHdRgj2HLvT4nJKjr7KIh-fFgwCjH6SEEPHLc0SaOTxelwnYkIIn3uJZGCIcBuNe1d8OIBz08UPbRvz8z-yHk3IliQBBQwSroKR5CNj43DzSU9M4W3jJQ1y64BA2MxkPc5qQ4g9l4811Jz-4FteI'],
            'k-02-dropped-raglan-longline' => ['category' => 'oversized', 'material' => '320 GSM FRENCH TERRY', 'image' => 'https://lh3.googleusercontent.com/aida-public/AB6AXuDEZjNnRfaOW-bWktd7IemrKw2QUgXxHaaKJ9_xycE4NrEgnsKZryjdG7yRUCYi_hl6huCP4Nq0ARvsWG1VN6i8wdWZHDw0-sNHH55a8yZr02kwHbS6x-sgVwzQcMlrDLZQYZQClAfhAyaBKPou55b_Stj_4iF6IkWjvo-2iV3FIgT7sTV3ThXlKd97H-12TyjkfUMFTB-xS3aYSFdVwbtIOCQP_AsOqEofUrXCRoI7w7UZyeAqs44d'],
            'geometric-carabiner-key-tether' => ['category' => 'accessories', 'material' => 'GRADE 5 TITANIUM', 'image' => 'https://lh3.googleusercontent.com/aida-public/AB6AXuDZR9MeI3tKNUXUOfOlEwUm72c_uoP47vON0i0odBcCkWYoW2CDhPXcIvosfSmypFjpepDXpozWIiR1Lql0Zm8l_vPnOmy6BdDzxnk156pjKElzT51jiMJ4TXXmcyIw-M3fe5fJ72ngYZ3wVYd-y6GXmtr7YIZ8iEsNC_1d-23mrNhODWzfsl2rmQL0BrxthImpgxZhxMedZE6DRTDeGBciaYinLunVpv-xKdSA3wqJLUN1mvE6UCVf'],
        ];
        $localImages = ['k-05-structural-raglan-crew', 'k-06-washed-cargo-tee', 'k-07-raw-cut-box-tee', 'k-08-sculpted-concrete-hoodie', 'k-09-modular-field-cargo', 'k-10-wide-pleat-trouser', 'k-11-modular-chest-harness'];

        return collect($items)->mapWithKeys(function (array $item) use ($defaults, $localImages) {
            $default = $defaults[$item['sku']] ?? [];
            $image = $item['image'] ?? ($default['image'] ?? null);
            if (! $image && in_array($item['sku'], $localImages, true)) {
                $image = asset('images/products/' . $item['sku'] . '.png');
            }

            return [$item['sku'] => [...$item, 'category' => $default['category'] ?? 'general', 'material' => $default['material'] ?? 'ARCHIVAL MATERIAL', 'image' => $image]];
        })->all();
    }

    private function inventoryImageUrl(?string $image): ?string
    {
        if (! $image) {
            return null;
        }

        return str_starts_with($image, 'http') || str_starts_with($image, '/')
            ? $image
            : Storage::url($image);
    }

    private function readSupportMessages(): array
    {
        $contents = Storage::disk('local')->get('support_messages.json', '[]');
        $messages = json_decode($contents, true);

        return is_array($messages) ? $messages : [];
    }

    private function readLocalReviews(): array
    {
        $contents = Storage::disk('local')->get('product_reviews.json', '{}');
        $reviews = json_decode($contents, true);

        return is_array($reviews) ? $reviews : [];
    }

    private function writeLocalReviews(array $reviews): void
    {
        Storage::disk('local')->put('product_reviews.json', json_encode($reviews, JSON_PRETTY_PRINT));
    }

    private function readLocalOrders(): array
    {
        $contents = Storage::disk('local')->get('orders.json', '[]');
        $orders = json_decode($contents, true);

        return is_array($orders) ? $orders : [];
    }

    private function writeLocalOrders(array $orders): void
    {
        Storage::disk('local')->put('orders.json', json_encode(array_values($orders), JSON_PRETTY_PRINT));
    }

    public function dashboard(Request $request): View
    {
        if (! $request->session()->has('customer_login.email')) {
            abort(redirect()->route('login'));
        }

        $cart = $request->session()->get('cart', []);
        $loginEmail = strtolower((string) $request->session()->get('customer_login.email'));
        $accounts = $request->session()->get('customer_accounts', []);
        $account = $accounts[$loginEmail] ?? [];
        $customer = $account['profile'] ?? $request->session()->get('customer_profile', []);
        $accountOrders = $request->session()->get('customer_orders', []);
        $lastOrder = collect($accountOrders[$loginEmail] ?? [])->last();

        if (! $this->databaseIsConfigured()) {
            $lastOrder = collect($this->readLocalOrders())
                ->filter(fn (array $order) => strtolower((string) data_get($order, 'customer.email')) === $loginEmail)
                ->last() ?: $lastOrder;
        }

        if ($loginEmail && $this->databaseIsConfigured()) {
            try {
                $user = User::where('email', $loginEmail)->first();
                $databaseOrder = Order::where('email', $loginEmail)->latest('placed_at')->first();

                if ($user) {
                    [$fallbackFirstName, $fallbackLastName] = array_pad(explode(' ', trim($user->name), 2), 2, '');
                    $customer = array_merge($customer, array_filter([
                        'first_name' => $user->first_name ?: $fallbackFirstName,
                        'last_name' => $user->last_name ?: $fallbackLastName,
                        'email' => $user->email,
                        'phone' => $user->phone,
                        'address' => $user->address,
                        'barangay' => $user->barangay,
                        'city' => $user->city,
                        'province' => $user->province,
                        'postal_code' => $user->postal_code,
                        'avatar' => $user->avatar,
                    ], static fn ($value) => $value !== null && $value !== ''));
                }

                if ($databaseOrder) {
                    $lastOrder = [
                        'id' => $databaseOrder->id,
                        'number' => $databaseOrder->order_number,
                        'cart' => $databaseOrder->cart,
                        'subtotal' => (float) $databaseOrder->subtotal,
                        'customer' => $databaseOrder->only([
                            'email', 'first_name', 'last_name', 'phone', 'address',
                            'barangay', 'city', 'province', 'postal_code', 'payment_method',
                        ]),
                        'status' => $databaseOrder->status,
                        'placed_at' => optional($databaseOrder->placed_at)->format('M d, Y h:i A'),
                    ];
                    $customer = array_merge($lastOrder['customer'], $customer);
                }
            } catch (QueryException $exception) {
                report($exception);
            }
        }

        // In the local fallback, never show another account's order.
        if (! $lastOrder) {
            $lastOrder = collect($accountOrders[$loginEmail] ?? [])->last();
        }

        $cartSubtotal = collect($cart)->sum(fn ($item) => (float) str_replace([',', '₱'], '', $item['price']) * $item['quantity']);

        return view('dashboard', [
            'cart' => $cart,
            'lastOrder' => $lastOrder,
            'cartSubtotal' => $cartSubtotal,
            'customer' => $customer,
        ]);
    }

    public function updateCustomerProfile(Request $request)
    {
        if (! $request->session()->has('customer_login.email')) {
            return redirect()->route('login');
        }

        $currentEmail = $request->session()->get('customer_login.email');

        if (! $currentEmail) {
            return redirect()->route('login')->withErrors(['email' => 'Please sign in before editing your account.']);
        }

        $data = $request->validate([
            'first_name' => ['required', 'string', 'max:80'],
            'last_name' => ['required', 'string', 'max:80'],
            'email' => ['required', 'email:rfc', 'max:255'],
            'phone' => ['required', 'string', 'max:40'],
            'address' => ['required', 'string', 'max:255'],
            'barangay' => ['required', 'string', 'max:100'],
            'city' => ['required', 'string', 'max:100'],
            'province' => ['required', 'string', 'max:100'],
            'postal_code' => ['required', 'string', 'max:20'],
            'avatar' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'remove_avatar' => ['nullable', 'boolean'],
        ]);

        if ($request->hasFile('avatar')) {
            $data['avatar'] = Storage::url($request->file('avatar')->store('avatars', 'public'));
        } elseif ($request->boolean('remove_avatar')) {
            $data['avatar'] = null;
        } elseif ($request->session()->filled('customer_profile.avatar')) {
            $data['avatar'] = $request->session()->get('customer_profile.avatar');
        }

        if ($this->databaseIsConfigured()) {
            try {
                $user = User::where('email', $currentEmail)->first();
                $user?->update([
                    'name' => trim($data['first_name'] . ' ' . $data['last_name']),
                    ...$data,
                ]);
            } catch (QueryException $exception) {
                report($exception);
            }
        } else {
            $accounts = $request->session()->get('customer_accounts', []);
            $account = $accounts[strtolower($currentEmail)] ?? ['password' => null, 'profile' => []];
            $newEmail = strtolower($data['email']);
            $account['profile'] = $data;
            $accounts[$newEmail] = $account;
            if ($newEmail !== strtolower($currentEmail)) {
                unset($accounts[strtolower($currentEmail)]);
            }
            $request->session()->put('customer_accounts', $accounts);
        }

        $request->session()->put('customer_login.email', $data['email']);
        $request->session()->put('customer_profile', $data);

        return redirect()->route('dashboard')->with('profile_success', 'Account details updated.');
    }

    public function login(): View
    {
        return view('login');
    }

    public function forgotPassword(): View
    {
        return view('forgot-password');
    }

    public function sendForgotPassword(Request $request)
    {
        $data = $request->validate([
            'email' => ['required', 'email', 'max:255'],
            'channel' => ['required', 'in:email,sms'],
            'phone' => ['required_if:channel,sms', 'nullable', 'string', 'max:30'],
        ]);

        $code = (string) random_int(100000, 999999);
        $request->session()->put('password_reset', [
            'email' => $data['email'],
            'channel' => $data['channel'],
            'phone' => $data['phone'] ?? null,
            'code' => $code,
            'created_at' => now()->toDateTimeString(),
        ]);

        $destination = $data['channel'] === 'sms' ? ($data['phone'] ?? 'your phone') : $data['email'];

        return back()->with('password_reset_success', "A verification code was prepared for {$destination} via " . strtoupper($data['channel']) . '.')
            ->with('password_reset_demo_code', $code);
    }

    public function resetPassword(Request $request)
    {
        $data = $request->validate([
            'code' => ['required', 'digits:6'],
            'password' => ['required', 'string', 'min:8', 'max:255', 'confirmed'],
        ]);
        $reset = $request->session()->get('password_reset');

        if (! is_array($reset) || ! hash_equals((string) ($reset['code'] ?? ''), $data['code'])) {
            throw ValidationException::withMessages(['code' => 'That verification code is invalid.']);
        }

        if (! empty($reset['created_at']) && now()->diffInMinutes($reset['created_at']) > 15) {
            $request->session()->forget('password_reset');
            throw ValidationException::withMessages(['code' => 'That verification code has expired. Request a new one.']);
        }

        $email = strtolower((string) $reset['email']);
        $updated = false;

        if ($this->databaseIsConfigured()) {
            try {
                $user = User::where('email', $email)->first();
                if ($user) {
                    $user->update(['password' => $data['password']]);
                    $updated = true;
                }
            } catch (QueryException $exception) {
                report($exception);
            }
        } else {
            $accounts = $request->session()->get('customer_accounts', []);
            if (isset($accounts[$email])) {
                $accounts[$email]['password'] = Hash::make($data['password']);
                $request->session()->put('customer_accounts', $accounts);
                $updated = true;
            }
        }

        if (! $updated) {
            throw ValidationException::withMessages(['email' => 'No customer account was found for that email.']);
        }

        $request->session()->forget('password_reset');

        return redirect()->route('login')->with('password_reset_complete', 'Password changed successfully. You can now sign in.');
    }

    public function authenticate(Request $request)
    {
        $data = $request->validate([
            'email' => ['required', 'email', 'max:255'],
            'password' => ['required', 'string', 'min:6', 'max:255'],
            'remember' => ['nullable', 'boolean'],
        ]);

        $account = null;

        if ($this->databaseIsConfigured()) {
            try {
                $account = User::where('email', $data['email'])->first();
            } catch (QueryException $exception) {
                report($exception);
            }
        } else {
            $accounts = $request->session()->get('customer_accounts', []);
            $account = $accounts[strtolower($data['email'])] ?? null;
        }

        $storedPassword = $account instanceof User ? $account->password : data_get($account, 'password');

        if (! $storedPassword || ! Hash::check($data['password'], $storedPassword)) {
            throw ValidationException::withMessages([
                'email' => 'These customer credentials are not recognized.',
            ]);
        }

        $request->session()->regenerate();
        $request->session()->put('customer_login', [
            'email' => strtolower($data['email']),
            'remember' => $request->boolean('remember'),
        ]);

        if (is_array($account) && isset($account['profile'])) {
            $request->session()->put('customer_profile', $account['profile']);
        } elseif ($account instanceof User) {
            $request->session()->put('customer_profile', [
                'first_name' => $account->first_name,
                'last_name' => $account->last_name,
                'email' => $account->email,
                'phone' => $account->phone,
                'address' => $account->address,
                'barangay' => $account->barangay,
                'city' => $account->city,
                'province' => $account->province,
                'postal_code' => $account->postal_code,
                'avatar' => $account->avatar,
            ]);
        }

        return redirect()->route('dashboard')->with('login_success', 'Client access authenticated. Welcome to the KRUZO archive.');
    }

    public function logoutCustomer(Request $request)
    {
        $request->session()->forget(['customer_login', 'customer_profile']);
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('logout_success', 'You have been signed out.');
    }

    public function register(): View
    {
        return view('register');
    }

    public function storeRegistration(Request $request)
    {
        $data = $request->validate([
            'first_name' => ['required', 'string', 'max:80'],
            'last_name' => ['required', 'string', 'max:80'],
            'phone' => ['required', 'string', 'max:30'],
            'email' => ['required', 'email', 'max:255'],
            'password' => ['required', 'string', 'min:8', 'max:255'],
            'welcome_credit' => ['nullable', 'boolean'],
            'drop_alerts' => ['nullable', 'boolean'],
            'terms' => ['accepted'],
        ]);

        // This project is a session-based storefront prototype. Store only
        // customer profile data here; real credentials belong in a database.
        $profile = [
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'email' => $data['email'],
            'phone' => '+63 ' . ltrim($data['phone'], '+630'),
            'welcome_credit' => $request->boolean('welcome_credit'),
            'drop_alerts' => $request->boolean('drop_alerts'),
            'address' => 'Add a saved delivery address',
            'city' => 'Metro Manila',
            'province' => 'Philippines',
            'postal_code' => '0000',
            'payment_method' => 'gcash',
        ];

        if ($this->databaseIsConfigured()) {
            try {
                User::updateOrCreate(
                    ['email' => $profile['email']],
                    [
                        'name' => trim($profile['first_name'] . ' ' . $profile['last_name']),
                        'password' => $data['password'],
                        ...$profile,
                    ],
                );
            } catch (QueryException $exception) {
                report($exception);
            }
        } else {
            $accounts = $request->session()->get('customer_accounts', []);
            $accounts[strtolower($profile['email'])] = [
                'password' => Hash::make($data['password']),
                'profile' => $profile,
            ];
            $request->session()->put('customer_accounts', $accounts);
        }

        $request->session()->regenerate();
        $request->session()->put('customer_profile', $profile);
        $request->session()->put('customer_login', [
            'email' => strtolower($profile['email']),
            'remember' => true,
        ]);

        return redirect()->route('dashboard')->with('login_success', 'Account created successfully. Welcome to the KRUZO customer dashboard.');
    }

    public function adminLogin(): View
    {
        return view('admin-login');
    }

    public function authenticateAdmin(Request $request)
    {
        $data = $request->validate([
            'operator_id' => ['required', 'string', 'max:255'],
            'passkey' => ['required', 'string', 'min:8', 'max:255'],
            'division' => ['required', 'in:dispatch,vault,concierge,finance,executive,ceo,admin'],
            'trusted_session' => ['nullable', 'boolean'],
        ]);

        $adminEmail = strtolower((string) env('ADMIN_EMAIL', ''));
        $operatorEmail = strtolower($data['operator_id']);
        $adminCredentials = array_filter([
            $adminEmail => (string) env('ADMIN_PASSWORD_HASH', ''),
            'pryvstpedrera@gmail.com' => (string) env('ADMIN_PASSWORD_HASH', ''),
            strtolower((string) env('ADMIN_EMAIL_2', '')) => (string) env('ADMIN_PASSWORD_HASH_2', ''),
        ], static fn (string $hash, string $email): bool => $email !== '' && $hash !== '', ARRAY_FILTER_USE_BOTH);
        $isWhitelisted = isset($adminCredentials[$operatorEmail])
            && Hash::check($data['passkey'], $adminCredentials[$operatorEmail]);

        if (! $isWhitelisted) {
            throw ValidationException::withMessages([
                'operator_id' => 'The admin email or passkey is not authorized.',
            ]);
        }

        $request->session()->regenerate();
        $request->session()->put('admin_login', [
            'operator_id' => strtolower($data['operator_id']),
            'division' => $operatorEmail === strtolower((string) env('ADMIN_EMAIL_2', ''))
                ? 'admin'
                : (in_array($operatorEmail, [$adminEmail, 'pryvstpedrera@gmail.com'], true) ? 'ceo' : $data['division']),
            'trusted_session' => $request->boolean('trusted_session'),
        ]);

        return redirect()->route('admin.dashboard')->with('admin_login_success', 'Atelier operations session authenticated.');
    }

    public function logoutAdmin(Request $request)
    {
        $request->session()->forget('admin_login');
        $request->session()->regenerateToken();

        return redirect()->route('admin.login')->with('admin_logout_success', 'Admin account signed out.');
    }

    private function paymentMethodLabel(?string $method): string
    {
        return match ($method) {
            'gcash' => 'GCASH / MAYA',
            'bdo' => 'BDO ONLINE BANKING',
            'bpi' => 'BPI ONLINE BANKING',
            'card' => 'VISA / MASTERCARD',
            default => 'CASH ON DELIVERY',
        };
    }

    public function adminDashboard(Request $request): View
    {
        if (! $request->session()->has('admin_login')) {
            abort(redirect()->route('home'));
        }

        $orders = [];
        $inventory = [];
        $settlements = [];
        $revenue = 0;
        $orderCount = 0;
        $inventoryCount = 0;
        $lowStockCount = 0;

        if ($this->databaseIsConfigured()) {
            try {
                $databaseOrders = Order::latest('placed_at')->get();
                $orders = $databaseOrders->map(function (Order $order): array {
                    $items = collect($order->cart)->map(fn ($item) => $item['quantity'] . 'x ' . $item['name'])->implode(' + ');

                    return [
                        'number' => $order->order_number,
                        'customer' => trim($order->first_name . ' ' . $order->last_name),
                        'email' => $order->email,
                        'destination' => trim($order->address . ', ' . $order->barangay . ', ' . $order->city),
                        'items' => $items,
                        'courier' => 'PENDING ASSIGNMENT',
                        'payment' => $this->paymentMethodLabel($order->payment_method),
                        'total' => '₱' . number_format((float) $order->subtotal, 2),
                        'status_key' => $order->status,
                        'status' => strtoupper(str_replace('_', ' ', $order->status)),
                    ];
                })->all();
                $revenue = (float) $databaseOrders->sum('subtotal');
                $orderCount = $databaseOrders->count();

                $inventory = InventoryItem::orderBy('stock')->get()->all();
                $inventoryCount = collect($inventory)->sum('stock');
                $lowStockCount = collect($inventory)->filter(fn (InventoryItem $item) => $item->stock <= $item->low_stock_threshold)->count();
                $settlements = $databaseOrders->groupBy('payment_method')->map(fn ($group) => [
                    'name' => $this->paymentMethodLabel($group->first()->payment_method),
                    'total' => '₱' . number_format((float) $group->sum('subtotal'), 2),
                ])->values()->all();
            } catch (QueryException $exception) {
                report($exception);
            }
        }
        // Keep every local prototype order visible across customer/admin views.
        if (! $orders) {
            $sessionOrders = collect($this->readLocalOrders())
                ->reject(fn (array $order) => $this->isAutomatedTestOrder($order))
                ->concat(collect($request->session()->get('customer_orders', []))
                ->flatMap(fn (array $customerOrders) => $customerOrders)
                ->values())
                ->unique('number')
                ->values();
            if ($sessionOrders->isEmpty() && ($sessionOrder = $request->session()->get('last_order'))) {
                $sessionOrders = collect([$sessionOrder]);
            }

            $orders = $sessionOrders->map(function (array $sessionOrder): array {
                $customer = $sessionOrder['customer'];
                $status = $sessionOrder['status'] ?? 'order_received';

                return [
                    'number' => $sessionOrder['number'],
                    'customer' => trim($customer['first_name'] . ' ' . $customer['last_name']),
                    'email' => $customer['email'] ?? '',
                    'destination' => trim($customer['address'] . ', ' . $customer['barangay'] . ', ' . $customer['city']),
                    'items' => collect($sessionOrder['cart'])->map(fn ($item) => $item['quantity'] . 'x ' . $item['name'])->implode(' + '),
                    'courier' => 'PENDING ASSIGNMENT',
                    'payment' => $this->paymentMethodLabel($customer['payment_method'] ?? 'cod'),
                    'total' => '₱' . number_format((float) $sessionOrder['subtotal'], 2),
                    'status_key' => $status,
                    'status' => strtoupper(str_replace('_', ' ', $status)),
                ];
            })->all();
            $revenue = (float) $sessionOrders->sum('subtotal');
            $orderCount = $sessionOrders->count();
            $settlements = $sessionOrders->groupBy(fn (array $order) => $order['customer']['payment_method'] ?? 'cod')
                ->map(fn ($group) => [
                    'name' => $this->paymentMethodLabel($group->first()['customer']['payment_method'] ?? 'cod'),
                    'total' => '₱' . number_format((float) $group->sum('subtotal'), 2),
                ])->values()->all();
        }

        return view('admin-dashboard', [
            'orders' => $orders,
            'inventory' => $inventory,
            'settlements' => $settlements,
            'revenue' => $revenue,
            'orderCount' => $orderCount,
            'averageOrderValue' => $orderCount ? $revenue / $orderCount : 0,
            'inventoryCount' => $inventoryCount,
            'lowStockCount' => $lowStockCount,
        ]);
    }

    private function isAutomatedTestOrder(array $order): bool
    {
        return strtolower((string) data_get($order, 'customer.email')) === 'customer@example.com';
    }

    public function updateOrderStatus(Request $request, string $order)
    {
        if (! $request->session()->has('admin_login')) {
            return redirect()->route('home');
        }

        $data = $request->validate([
            'status' => ['required', 'in:order_received,processing,shipped,delivered,cancelled'],
        ]);
        $updated = false;

        if ($this->databaseIsConfigured()) {
            try {
                $updated = (bool) Order::where('order_number', $order)->update(['status' => $data['status']]);
            } catch (QueryException $exception) {
                report($exception);
            }
        }

        $sessionOrder = $request->session()->get('last_order');
        if ($sessionOrder && ($sessionOrder['number'] ?? null) === $order) {
            $sessionOrder['status'] = $data['status'];
            $request->session()->put('last_order', $sessionOrder);
            $updated = true;
        }

        $customerOrders = $request->session()->get('customer_orders', []);
        foreach ($customerOrders as $email => $orders) {
            foreach ($orders as $index => $customerOrder) {
                if (($customerOrder['number'] ?? null) === $order) {
                    $customerOrders[$email][$index]['status'] = $data['status'];
                    $updated = true;
                }
            }
        }
        $request->session()->put('customer_orders', $customerOrders);

        if (! $this->databaseIsConfigured()) {
            $localOrders = collect($this->readLocalOrders())->map(function (array $localOrder) use ($order, $data) {
                if (($localOrder['number'] ?? null) === $order) {
                    $localOrder['status'] = $data['status'];
                }
                return $localOrder;
            })->all();
            $this->writeLocalOrders($localOrders);
        }

        return redirect()->route('admin.dashboard')->with(
            $updated ? 'status_success' : 'status_error',
            $updated ? 'Order status updated.' : 'Order could not be found.',
        );
    }

    public function adminInventory(Request $request): View
    {
        if (! $request->session()->has('admin_login')) {
            abort(redirect()->route('home'));
        }

        $inventory = collect($request->session()->get('admin_inventory', $this->localInventory()))
            ->map(fn (array $item) => new InventoryItem($item));

        if ($this->databaseIsConfigured()) {
            try {
                $inventory = InventoryItem::orderBy('name')->get();
            } catch (QueryException $exception) {
                report($exception);
            }
        }

        return view('admin-inventory', compact('inventory'));
    }

    public function adminOrders(Request $request): View
    {
        return $this->adminSection($request, 'orders');
    }

    public function adminClientele(Request $request): View
    {
        return $this->adminSection($request, 'clientele');
    }

    public function adminFinancials(Request $request): View
    {
        return $this->adminSection($request, 'financials');
    }

    public function adminSettings(Request $request): View
    {
        return $this->adminSection($request, 'settings');
    }

    public function adminContact(Request $request): View
    {
        return $this->adminSection($request, 'contact');
    }

    private function adminSection(Request $request, string $section): View
    {
        if (! $request->session()->has('admin_login')) {
            abort(redirect()->route('home'));
        }

        $sections = [
            'orders' => ['title' => 'Live Orders & Dispatch', 'eyebrow' => 'FULFILLMENT OPERATIONS', 'description' => 'Review recorded customer orders and monitor the current dispatch pipeline.'],
            'clientele' => ['title' => 'Clientele & VIP Fittings', 'eyebrow' => 'CUSTOMER RELATIONSHIPS', 'description' => 'Review customer profiles and delivery details from completed orders.'],
            'financials' => ['title' => 'Financials & BIR', 'eyebrow' => 'SETTLEMENT TELEMETRY', 'description' => 'Review revenue and payment-method totals from recorded customer orders.'],
            'settings' => ['title' => 'System Settings', 'eyebrow' => 'ATELIER CONFIGURATION', 'description' => 'Review the active operations configuration for this local admin workspace.'],
            'contact' => ['title' => 'Contact / Feedback Inbox', 'eyebrow' => 'CLIENT CONCIERGE', 'description' => 'Read customer support requests, feedback, and contact submissions received by the atelier.'],
        ];

        abort_unless(isset($sections[$section]), 404);

        $orders = [];
        $messages = [];
        if ($this->databaseIsConfigured()) {
            try {
                $orders = Order::latest('placed_at')->get()->map(fn (Order $order) => [
                    'number' => $order->order_number,
                    'customer' => trim($order->first_name . ' ' . $order->last_name),
                    'total' => '₱' . number_format((float) $order->subtotal, 2),
                    'status' => strtoupper(str_replace('_', ' ', $order->status)),
                    'payment' => strtoupper($order->payment_method),
                ])->all();
                $messages = SupportMessage::latest()->get()->map(fn (SupportMessage $message) => [
                    'reference' => $message->reference,
                    'specialization' => strtoupper($message->specialization),
                    'name' => $message->name,
                    'contact' => $message->contact,
                    'channel' => strtoupper($message->channel),
                    'message' => $message->message,
                    'created_at' => optional($message->created_at)->format('M d, Y h:i A'),
                ])->all();
            } catch (QueryException $exception) {
                report($exception);
            }
        }

        if (! $messages) {
            $messages = collect($this->readSupportMessages())->reverse()->values()->all();
        }
        if (! $messages) {
            $messages = collect($request->session()->get('support_messages', []))->reverse()->values()->all();
        }

        if (! $orders && ($order = $request->session()->get('last_order'))) {
            $customer = $order['customer'] ?? [];
            $orders[] = [
                'number' => $order['number'],
                'customer' => trim(($customer['first_name'] ?? '') . ' ' . ($customer['last_name'] ?? '')),
                'total' => '₱' . number_format((float) ($order['subtotal'] ?? 0), 2),
                'status' => strtoupper(str_replace('_', ' ', $order['status'] ?? 'order_received')),
                'payment' => strtoupper($customer['payment_method'] ?? 'COD'),
            ];
        }

        return view('admin-section', [
            'section' => $section,
            'sectionInfo' => $sections[$section],
            'orders' => $orders,
            'revenue' => collect($orders)->sum(fn (array $order) => (float) str_replace(['₱', ','], '', $order['total'])),
            'messages' => $messages,
        ]);
    }

    public function storeInventory(Request $request)
    {
        if (! $request->session()->has('admin_login')) {
            return redirect()->route('home');
        }

        $data = $request->validate([
            'sku' => ['required', 'string', 'max:100'],
            'name' => ['required', 'string', 'max:255'],
            'price' => ['required', 'numeric', 'min:0'],
            'stock' => ['required', 'integer', 'min:0'],
            'low_stock_threshold' => ['required', 'integer', 'min:0'],
            'image' => ['nullable', 'image', 'max:5120'],
        ]);

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('products', 'public');
        }

        if ($this->databaseIsConfigured()) {
            try {
                InventoryItem::create($data);
            } catch (QueryException $exception) {
                report($exception);

                return redirect()->route('admin.inventory')->with('inventory_error', 'Product could not be added. Check the SKU and database configuration.');
            }
        } else {
            $items = $request->session()->get('admin_inventory', $this->localInventory());
            $items[] = $data;
            $request->session()->put('admin_inventory', $items);
        }

        return redirect()->route('admin.inventory')->with('inventory_success', 'Product added to inventory.');
    }

    public function updateInventory(Request $request, string $item)
    {
        if (! $request->session()->has('admin_login')) {
            return redirect()->route('home');
        }

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'price' => ['required', 'numeric', 'min:0'],
            'stock' => ['required', 'integer', 'min:0'],
            'low_stock_threshold' => ['required', 'integer', 'min:0'],
            'image' => ['nullable', 'image', 'max:5120'],
        ]);

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('products', 'public');
        }

        if ($this->databaseIsConfigured()) {
            try {
                $record = InventoryItem::where('sku', $item)->first();

                if (! $record) {
                    return redirect()->route('admin.inventory')->with('inventory_error', 'Product could not be found.');
                }

                $record->update($data);
            } catch (QueryException $exception) {
                report($exception);

                return redirect()->route('admin.inventory')->with('inventory_error', 'Product could not be updated. Check the database configuration.');
            }
        } else {
            $items = $request->session()->get('admin_inventory', $this->localInventory());
            $found = false;

            foreach ($items as $index => $record) {
                if (($record['sku'] ?? null) === $item) {
                    $items[$index] = [...$record, ...$data];
                    $found = true;
                    break;
                }
            }

            if (! $found) {
                return redirect()->route('admin.inventory')->with('inventory_error', 'Product could not be found.');
            }

            $request->session()->put('admin_inventory', $items);
        }

        return redirect()->route('admin.inventory')->with('inventory_success', 'Product updated.');
    }

    public function deleteInventory(Request $request, string $item)
    {
        if (! $request->session()->has('admin_login')) {
            return redirect()->route('home');
        }

        if ($this->databaseIsConfigured()) {
            try {
                $deleted = (bool) InventoryItem::where('sku', $item)->delete();

                return redirect()->route('admin.inventory')->with(
                    $deleted ? 'inventory_success' : 'inventory_error',
                    $deleted ? 'Product removed from inventory.' : 'Product could not be found.',
                );
            } catch (QueryException $exception) {
                report($exception);

                return redirect()->route('admin.inventory')->with('inventory_error', 'Product could not be removed. Check the database configuration.');
            }
        }

        $items = $request->session()->get('admin_inventory', $this->localInventory());
        $remaining = array_values(array_filter($items, fn (array $record) => ($record['sku'] ?? null) !== $item));

        if (count($remaining) === count($items)) {
            return redirect()->route('admin.inventory')->with('inventory_error', 'Product could not be found.');
        }

        $request->session()->put('admin_inventory', $remaining);

        return redirect()->route('admin.inventory')->with('inventory_success', 'Product removed from inventory.');
    }

    public function cart(Request $request): View
    {
        $cart = $this->cartWithLocalImages($request->session()->get('cart', []));
        $request->session()->put('cart', $cart);

        return view('cart', compact('cart'));
    }

    private function cartWithLocalImages(array $cart): array
    {
        $images = [
            'k-01-structural-boxy-tee' => 'k-07-raw-cut-box-tee.png',
            'brutalist-monolith-cuff-ring-set' => 'k-11-modular-chest-harness.png',
            'k-02-dropped-raglan-longline' => 'k-06-washed-cargo-tee.png',
            'geometric-carabiner-key-tether' => 'k-11-modular-chest-harness.png',
            'k-03-wide-leg-pleated-cargo' => 'k-09-modular-field-cargo.png',
            'atelier-heavyweight-tank' => 'k-06-washed-cargo-tee.png',
            'modular-crossbody-chest-rig' => 'k-11-modular-chest-harness.png',
            'k-04-sculpted-oversized-hoodie' => 'k-08-sculpted-concrete-hoodie.png',
            'k-05-structural-raglan-crew' => 'k-05-structural-raglan-crew.png',
            'k-06-washed-cargo-tee' => 'k-06-washed-cargo-tee.png',
            'k-07-raw-cut-box-tee' => 'k-07-raw-cut-box-tee.png',
            'k-08-sculpted-concrete-hoodie' => 'k-08-sculpted-concrete-hoodie.png',
            'k-09-modular-field-cargo' => 'k-09-modular-field-cargo.png',
            'k-10-wide-pleat-trouser' => 'k-10-wide-pleat-trouser.png',
            'k-11-modular-chest-harness' => 'k-11-modular-chest-harness.png',
        ];

        return collect($cart)->map(function (array $item) use ($images): array {
            $slug = $item['slug'] ?? '';
            if (isset($images[$slug])) {
                $item['image'] = asset('images/products/' . $images[$slug]);
            } elseif (filter_var($item['image'] ?? null, FILTER_VALIDATE_URL) && ! str_starts_with((string) $item['image'], url('/'))) {
                $item['image'] = asset('images/products/k-07-raw-cut-box-tee.png');
            }

            return $item;
        })->all();
    }

    public function cartSummary(Request $request)
    {
        $cart = collect($request->session()->get('cart', []));
        $count = (int) $cart->sum('quantity');
        $total = $cart->sum(fn (array $item) => (float) str_replace([',', '₱'], '', $item['price']) * (int) $item['quantity']);

        return response()->json(['count' => $count, 'total' => $total]);
    }

    public function checkout(Request $request): View
    {
        $cart = $this->cartWithLocalImages($request->session()->get('cart', []));
        $subtotal = collect($cart)->sum(fn ($item) => (float) str_replace([',', '₱'], '', $item['price']) * $item['quantity']);
        $discount = $request->session()->get('coupon.code') === 'KRUZO250' ? 250 : 0;

        return view('checkout', compact('cart', 'subtotal', 'discount'));
    }

    public function applyCoupon(Request $request)
    {
        $data = $request->validate(['coupon_code' => ['required', 'string', 'max:40']]);
        $code = strtoupper(trim($data['coupon_code']));

        if ($code !== 'KRUZO250') {
            return back()->withErrors(['coupon_code' => 'That coupon code is not valid.']);
        }

        $request->session()->put('coupon', ['code' => 'KRUZO250', 'amount' => 250]);

        return back()->with('coupon_success', 'CONGRATULATIONS! Your free coupon KRUZO250 is claimed: ₱250.00 off your initial order.');
    }

    public function placeOrder(Request $request)
    {
        $cart = $this->cartWithLocalImages($request->session()->get('cart', []));

        if (count($cart) === 0) {
            return redirect()->route('cart');
        }

        $data = $request->validate([
            'email' => ['required', 'email:rfc', 'max:255'],
            'first_name' => ['required', 'string', 'max:80'],
            'last_name' => ['required', 'string', 'max:80'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'address' => ['required', 'string', 'max:255'],
            'barangay' => ['required', 'string', 'max:100'],
            'city' => ['required', 'string', 'max:100'],
            'province' => ['required', 'string', 'max:100'],
            'postal_code' => ['required', 'string', 'max:20'],
            'phone' => ['required', 'string', 'max:40'],
            'payment_method' => ['required', 'in:cod,gcash,bdo,bpi,card'],
        ]);

        $subtotal = collect($cart)->sum(fn ($item) => (float) str_replace([',', '₱'], '', $item['price']) * $item['quantity']);
        $discount = $request->session()->get('coupon.code') === 'KRUZO250' ? 250 : 0;
        $total = max(0, $subtotal - $discount);
        $placedAt = now();
        $orderNumber = 'KRZ-MNL-' . $placedAt->format('His');
        $customer = collect($data)->except(['password', 'password_confirmation'])->all();
        $savedOrderId = null;

        // The browser-only test environment has no PDO driver; configured deployments persist both records.
        if ($this->databaseIsConfigured()) {
            try {
                $user = User::updateOrCreate(
                    ['email' => $data['email']],
                    [
                        'name' => trim($data['first_name'] . ' ' . $data['last_name']),
                        'first_name' => $data['first_name'],
                        'last_name' => $data['last_name'],
                        'password' => $data['password'],
                        'phone' => $data['phone'],
                        'address' => $data['address'],
                        'barangay' => $data['barangay'],
                        'city' => $data['city'],
                        'province' => $data['province'],
                        'postal_code' => $data['postal_code'],
                    ],
                );

                $savedOrder = Order::create([
                    'user_id' => $user->id,
                    'order_number' => $orderNumber,
                    ...$customer,
                    'status' => 'order_received',
                    'subtotal' => $total,
                    'cart' => $cart,
                    'placed_at' => $placedAt,
                ]);
                $savedOrderId = $savedOrder->id;

                foreach ($cart as $item) {
                    InventoryItem::where('sku', $item['slug'])->decrement('stock', (int) $item['quantity']);
                }
            } catch (QueryException $exception) {
                report($exception);
            }
        }

        $order = [
            'id' => $savedOrderId,
            'number' => $orderNumber,
            'cart' => $cart,
            'subtotal' => $total,
            'discount' => $discount,
            'customer' => $customer,
            'status' => 'order_received',
            'placed_at' => $placedAt->format('M d, Y h:i A'),
        ];

        $accountEmail = strtolower($customer['email']);
        $customerOrders = $request->session()->get('customer_orders', []);
        $customerOrders[$accountEmail][] = $order;
        $request->session()->put('customer_orders', $customerOrders);

        if (! $this->databaseIsConfigured()) {
            $localOrders = collect($this->readLocalOrders())
                ->reject(fn (array $localOrder) => ($localOrder['number'] ?? null) === $orderNumber)
                ->push($order)
                ->all();
            $this->writeLocalOrders($localOrders);
        }

        if (! $this->databaseIsConfigured()) {
            $accounts = $request->session()->get('customer_accounts', []);
            $account = $accounts[$accountEmail] ?? ['password' => Hash::make($data['password']), 'profile' => []];
            $account['profile'] = $customer;
            $accounts[$accountEmail] = $account;
            $request->session()->put('customer_accounts', $accounts);
        }

        $request->session()->put('last_order', $order);
        $request->session()->put('customer_profile', $customer);
        $request->session()->put('customer_login', [
            'email' => $customer['email'],
            'remember' => true,
        ]);
        $request->session()->forget('cart');
        $request->session()->forget('coupon');

        return redirect()->route('thank-you');
    }

    public function thankYou(Request $request): View
    {
        return view('thank-you', ['order' => $request->session()->get('last_order')]);
    }

    public function addToCart(Request $request)
    {
        $data = $request->validate([
            'slug' => ['required', 'string', 'max:100'],
            'name' => ['required', 'string', 'max:255'],
            'price' => ['required', 'string', 'max:30'],
            'image' => ['required', 'url', 'max:2048'],
            'size' => ['nullable', 'string', 'max:30'],
            'color' => ['nullable', 'string', 'max:60'],
        ]);

        $cart = $request->session()->get('cart', []);
        $item = [
            ...$data,
            'quantity' => ($cart[$data['slug']]['quantity'] ?? 0) + 1,
        ];
        $cart[$data['slug']] = $item;
        $cart = $this->cartWithLocalImages($cart);
        $request->session()->put('cart', $cart);

        return response()->json(['redirect' => route('cart')]);
    }

    public function removeFromCart(Request $request)
    {
        $data = $request->validate(['slug' => ['required', 'string', 'max:100']]);
        $cart = $request->session()->get('cart', []);
        unset($cart[$data['slug']]);
        $request->session()->put('cart', $cart);

        return redirect()->route('cart');
    }

    public function generate(Request $request): string
    {
        $request->validate([
            'business_type' => ['required', 'string', 'max:255'],
        ]);

        return 'Generated successfully';
    }
}
