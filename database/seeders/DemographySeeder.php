<?php

namespace Database\Seeders;

use App\Models\Demography;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DemographySeeder extends Seeder
{
    public function run(): void
    {

        Schema::disableForeignKeyConstraints();
        DB::beginTransaction();

        $country_names = [
            ['name' => 'Azad Jammu and Kashmir', 'urdu_name' => 'آزاد جموں و کشمیر'],
            ['name' => 'Afghanistan', 'urdu_name' => 'افغانستان'],
            ['name' => 'Albania', 'urdu_name' => 'البانیہ'],
            ['name' => 'Algeria', 'urdu_name' => 'الجیریا'],
            ['name' => 'American Samoa', 'urdu_name' => 'امریکی ساموا'],
            ['name' => 'Andorra', 'urdu_name' => 'انڈورا'],
            ['name' => 'Angola', 'urdu_name' => 'انگولا'],
            ['name' => 'Anguilla', 'urdu_name' => 'انجویلا'],
            ['name' => 'Antigua & Barbuda', 'urdu_name' => 'اینٹیگوا اور باربوڈا'],
            ['name' => 'Argentina', 'urdu_name' => 'ارجنٹائن'],
            ['name' => 'Armenia', 'urdu_name' => 'آرمینیا'],
            ['name' => 'Aruba', 'urdu_name' => 'اروبا'],
            ['name' => 'Australia', 'urdu_name' => 'آسٹریلیا'],
            ['name' => 'Austria', 'urdu_name' => 'آسٹریا'],
            ['name' => 'Azerbaijan', 'urdu_name' => 'آذربائیجان'],
            ['name' => 'Bahamas', 'urdu_name' => 'بہاماس'],
            ['name' => 'Bahrain', 'urdu_name' => 'بحرین'],
            ['name' => 'Bangladesh', 'urdu_name' => 'بنگلہ دیش'],
            ['name' => 'Barbados', 'urdu_name' => 'بارباڈوس'],
            ['name' => 'Belarus', 'urdu_name' => 'بیلاروس'],
            ['name' => 'Belgium', 'urdu_name' => 'بیلجیم'],
            ['name' => 'Belize', 'urdu_name' => 'بیلیز'],
            ['name' => 'Benin', 'urdu_name' => 'بینن'],
            ['name' => 'Bermuda', 'urdu_name' => 'برمودا'],
            ['name' => 'Bhutan', 'urdu_name' => 'بھوٹان'],
            ['name' => 'Bolivia', 'urdu_name' => 'بولیویا'],
            ['name' => 'Bosnia & Herzegovina', 'urdu_name' => 'بوسنیا اور ہرزیگوینا'],
            ['name' => 'Botswana', 'urdu_name' => 'بوٹسوانا'],
            ['name' => 'Brazil', 'urdu_name' => 'برازیل'],
            ['name' => 'British Virgin Is.', 'urdu_name' => 'برٹش ورجن آئی لینڈز'],
            ['name' => 'Brunei', 'urdu_name' => 'برونائی'],
            ['name' => 'Bulgaria', 'urdu_name' => 'بلغاریہ'],
            ['name' => 'Burkina Faso', 'urdu_name' => 'برکینا فاسو'],
            ['name' => 'Burma', 'urdu_name' => 'برما'],
            ['name' => 'Burundi', 'urdu_name' => 'برونڈی'],
            ['name' => 'Cambodia', 'urdu_name' => 'کمبوڈیا'],
            ['name' => 'Cameroon', 'urdu_name' => 'کیمرون'],
            ['name' => 'Canada', 'urdu_name' => 'کینیڈا'],
            ['name' => 'Cape Verde', 'urdu_name' => 'کیپ وردے'],
            ['name' => 'Cayman Islands', 'urdu_name' => 'جزائر کیمن'],
            ['name' => 'Central African Rep.', 'urdu_name' => 'وسطی افریقی جمہوریہ'],
            ['name' => 'Chad', 'urdu_name' => 'چاڈ'],
            ['name' => 'Chile', 'urdu_name' => 'چلی'],
            ['name' => 'China', 'urdu_name' => 'چین'],
            ['name' => 'Colombia', 'urdu_name' => 'کولمبیا'],
            ['name' => 'Comoros', 'urdu_name' => 'کوموروس'],
            ['name' => 'Congo', 'urdu_name' => 'کانگو'],
            ['name' => 'Costa Rica', 'urdu_name' => 'کوسٹا ریکا'],
            ['name' => 'Croatia', 'urdu_name' => 'کروشیا'],
            ['name' => 'Cuba', 'urdu_name' => 'کیوبا'],
            ['name' => 'Cyprus', 'urdu_name' => 'قبرص'],
            ['name' => 'Czech Republic', 'urdu_name' => 'جمہوریہ چیک'],
            ['name' => 'Denmark', 'urdu_name' => 'ڈنمارک'],
            ['name' => 'Egypt', 'urdu_name' => 'مصر'],
            ['name' => 'El Salvador', 'urdu_name' => 'ال سلواڈور'],
            ['name' => 'Equatorial Guinea', 'urdu_name' => 'استوائی گنی'],
            ['name' => 'Eritrea', 'urdu_name' => 'اریٹیریا'],
            ['name' => 'Estonia', 'urdu_name' => 'ایسٹونیا'],
            ['name' => 'Ethiopia', 'urdu_name' => 'ایتھوپیا'],
            ['name' => 'Finland', 'urdu_name' => 'فن لینڈ'],
            ['name' => 'France', 'urdu_name' => 'فرانس'],
            ['name' => 'Germany', 'urdu_name' => 'جرمنی'],
            ['name' => 'Greece', 'urdu_name' => 'یونان'],
            ['name' => 'India', 'urdu_name' => 'ہندوستان'],
            ['name' => 'Indonesia', 'urdu_name' => 'انڈونیشیا'],
            ['name' => 'Iran', 'urdu_name' => 'ایران'],
            ['name' => 'Iraq', 'urdu_name' => 'عراق'],
            ['name' => 'Ireland', 'urdu_name' => 'آئرلینڈ'],
            ['name' => 'Italy', 'urdu_name' => 'اٹلی'],
            ['name' => 'Japan', 'urdu_name' => 'جاپان'],
            ['name' => 'Jordan', 'urdu_name' => 'اردن'],
            ['name' => 'Kuwait', 'urdu_name' => 'کویت'],
            ['name' => 'Malaysia', 'urdu_name' => 'ملائیشیا'],
            ['name' => 'Maldives', 'urdu_name' => 'مالدیپ'],
            ['name' => 'Nepal', 'urdu_name' => 'نیپال'],
            ['name' => 'Netherlands', 'urdu_name' => 'نیدرلینڈز'],
            ['name' => 'New Zealand', 'urdu_name' => 'نیوزی لینڈ'],
            ['name' => 'Norway', 'urdu_name' => 'ناروے'],
            ['name' => 'Oman', 'urdu_name' => 'عمان'],
            ['name' => 'Pakistan', 'urdu_name' => 'پاکستان'],
            ['name' => 'Qatar', 'urdu_name' => 'قطر'],
            ['name' => 'Saudi Arabia', 'urdu_name' => 'سعودی عرب'],
            ['name' => 'South Africa', 'urdu_name' => 'جنوبی افریقہ'],
            ['name' => 'Spain', 'urdu_name' => 'اسپین'],
            ['name' => 'Sri Lanka', 'urdu_name' => 'سری لنکا'],
            ['name' => 'Sweden', 'urdu_name' => 'سویڈن'],
            ['name' => 'Switzerland', 'urdu_name' => 'سوئٹزرلینڈ'],
            ['name' => 'Syria', 'urdu_name' => 'شام'],
            ['name' => 'Turkey', 'urdu_name' => 'ترکی'],
            ['name' => 'United Arab Emirates', 'urdu_name' => 'متحدہ عرب امارات'],
            ['name' => 'United Kingdom', 'urdu_name' => 'متحدہ سلطنت'],
            ['name' => 'United States', 'urdu_name' => 'ریاستہائے متحدہ'],
            ['name' => 'Yemen', 'urdu_name' => 'یمن'],
            ['name' => 'Zimbabwe', 'urdu_name' => 'زمبابوے'],
        ];
        foreach ($country_names as $country) {
            Demography::create([
                'name' => $country['name'],
                'urdu_name' => $country['urdu_name'],
                'type' => 'COUNTRY',
                'parent_id' => null,
                'is_ajk_district' => null,
                //  'code' => '$country["code"]'
            ]);
        }

        // Regions
        $regions = [
            ['name' => 'Muzaffarabad', 'urdu_name' => 'مظفرآباد', 'code' => 'MZA'],
            ['name' => 'Mirpur', 'urdu_name' => 'میرپور', 'code' => 'MIR'],
            ['name' => 'Poonch', 'urdu_name' => 'پونچھ', 'code' => 'POO'],
        ];

        // Adding regions for Pakistan 

        $regions = [
            ['name' => 'Muzaffarabad', 'urdu_name' => 'مظفرآباد'],
            ['name' => 'Mirpur', 'urdu_name' => 'میرپور'],
            ['name' => 'Poonch', 'urdu_name' => 'پونچھ'],
        ];
        $regionIds = [];

        foreach ($regions as $region) {
            $createdRegion = Demography::create([
                'name' => $region['name'],
                'urdu_name' => $region['urdu_name'],
                'type' => 'REGION',
                'parent_id' => Demography::where('name', 'Azad Jammu and Kashmir')->first()->id,
                'is_ajk_district' => 1,
                'code' => ''
            ]);

            $regionIds[$region['name']] = $createdRegion->id;
        }

        // districts of AJK 

        $districtajk = [

            ['name' => 'Muzaffarabad', 'urdu_name' => 'مظفرآباد', 'code' => 822, 'region' => 'Muzaffarabad'],
            ['name' => 'Neelam', 'urdu_name' => 'نیلم', 'code' => 825, 'region' => 'Muzaffarabad'],
            ['name' => 'Haveli', 'urdu_name' => 'حویلی', 'code' => 826, 'region' => 'Muzaffarabad'],

            ['name' => 'Mirpur', 'urdu_name' => 'میرپور', 'code' => 813, 'region' => 'Mirpur'],
            ['name' => 'Kotli', 'urdu_name' => 'کوٹلی', 'code' => 812, 'region' => 'Mirpur'],
            ['name' => 'Bhimber', 'urdu_name' => 'بھمبر', 'code' => 811, 'region' => 'Mirpur'],

            ['name' => 'Jehlum Valley', 'urdu_name' => 'جہلم ویلی', 'code' => 827, 'region' => 'Poonch'],
            ['name' => 'Poonch', 'urdu_name' => 'پونچھ', 'code' => 823, 'region' => 'Poonch'],
            ['name' => 'Bagh', 'urdu_name' => 'باغ', 'code' => 821, 'region' => 'Poonch'],
            ['name' => 'Sudhanoti', 'urdu_name' => 'سدھنوتی', 'code' => 824, 'region' => 'Poonch'],
        ];

        $districtIds = [];

        foreach ($districtajk as $district) {

            $createdDistrict = Demography::create([
                'name' => $district['name'],
                'urdu_name' => $district['urdu_name'],
                'type' => 'DISTRICT',
                'parent_id' => $regionIds[$district['region']],
                'is_ajk_district' => 1,
                'code' => $district['code']
            ]);
            $districtIds[$district['name']] = $createdDistrict->id;
        }



        // Union Councils
        $unionCouncils = [
            ['name' => 'Ankar', 'urdu_name' => 'انکر', 'code' => 81311003, 'city' => 'Dadyal'],
            ['name' => 'Chatroh', 'urdu_name' => 'چھتروہ', 'code' => 81311019, 'city' => 'Dadyal'],
            ['name' => 'Kathar', 'urdu_name' => 'کٹھاڑ', 'code' => 81311031, 'city' => 'Dadyal'],
            ['name' => 'Onah', 'urdu_name' => 'اوناع', 'code' => 81311042, 'city' => 'Dadyal'],
            ['name' => 'Raipur', 'urdu_name' => 'رائے پور', 'code' => 81311050, 'city' => 'Dadyal'],
            ['name' => 'Ratta', 'urdu_name' => 'رٹہ', 'code' => 81311052, 'city' => 'Dadyal'],
            ['name' => 'Siakh', 'urdu_name' => 'سیاکھ', 'code' => 81311056, 'city' => 'Dadyal'],
            ['name' => 'Municipal Committee Dadyal', 'urdu_name' => 'میونسپل کمیٹی ڈڈیال', 'code' => 81312018, 'city' => 'Dadyal'],
            ['name' => 'Afzalpur', 'urdu_name' => 'افضل پور', 'code' => 81321001, 'city' => 'Chaksawari'],
            ['name' => 'Dhangri Bala', 'urdu_name' => 'ڈھانگری بالا', 'code' => 81321012, 'city' => 'Chaksawari'],
            ['name' => 'Municipal Committee Chaksawari', 'urdu_name' => 'میونسپل کمیٹی چکسواری', 'code' => 81321015, 'city' => 'Chaksawari'],
            ['name' => 'Kaneli', 'urdu_name' => 'کنیلی', 'code' => 81321030, 'city' => 'Chaksawari'],
            ['name' => 'Kharak', 'urdu_name' => 'کھاڑک', 'code' => 81321032, 'city' => 'Chaksawari'],
            ['name' => 'Khari Khas', 'urdu_name' => 'کھڑی خاص', 'code' => 81321034, 'city' => 'Chaksawari'],
            ['name' => 'Naugran', 'urdu_name' => 'نوگراں', 'code' => 81321041, 'city' => 'Chaksawari'],
            ['name' => 'Panyam', 'urdu_name' => 'پنیام', 'code' => 81321045, 'city' => 'Chaksawari'],
            ['name' => 'Pind Khurd', 'urdu_name' => 'پنڈخورد', 'code' => 81321046, 'city' => 'Chaksawari'],
            ['name' => 'Pindi Sabharwal', 'urdu_name' => 'پنڈی سبھروال', 'code' => 81321047, 'city' => 'Chaksawari'],
            ['name' => 'Potha Bainsi', 'urdu_name' => 'پوٹھہ بینسی', 'code' => 81321049, 'city' => 'Chaksawari'],
            ['name' => 'Rathoa Muhammad Ali', 'urdu_name' => 'رٹھوعہ محمد علی', 'code' => 81321051, 'city' => 'Chaksawari'],
            ['name' => 'Samwal Sharif', 'urdu_name' => 'سموال شریف', 'code' => 81321055, 'city' => 'Chaksawari'],
            ['name' => 'Municipal Corporation Mirpur', 'urdu_name' => 'میونسپل کارپوریشن میرپور', 'code' => 81322016, 'city' => 'Mirpur City'],
            ['name' => 'Municipal Committee Islamgarh', 'urdu_name' => 'میونسپل کمیٹی اسلام گڑھ', 'code' => 81322017, 'city' => 'Mirpur City'],
            ['name' => 'Tharian', 'urdu_name' => 'ٹھیریاں', 'code' => 82231008, 'city' => 'Muzaffarabad City'],
            ['name' => 'Chatter Domail', 'urdu_name' => 'چھتر دومیل', 'code' => 82231015, 'city' => 'Muzaffarabad City'],
            ['name' => 'Charakpura', 'urdu_name' => 'چڑکپورہ', 'code' => 82231018, 'city' => 'Muzaffarabad City'],
            ['name' => 'Chatter Kalas', 'urdu_name' => 'چھتر کلاس', 'code' => 82231019, 'city' => 'Muzaffarabad City'],
            ['name' => 'Danna', 'urdu_name' => 'ڈنہ', 'code' => 82231022, 'city' => 'Muzaffarabad City'],
            ['name' => 'Gojra', 'urdu_name' => 'گوجرہ', 'code' => 82231027, 'city' => 'Muzaffarabad City'],
            ['name' => 'Hattian Dupatta', 'urdu_name' => 'ہٹیاں دوپٹہ', 'code' => 82231031, 'city' => 'Muzaffarabad City'],
            ['name' => 'Heer Kotli', 'urdu_name' => 'ہیر کوٹلی', 'code' => 82231032, 'city' => 'Muzaffarabad City'],
            ['name' => 'Jhandgran', 'urdu_name' => 'جھنڈگراں', 'code' => 82231035, 'city' => 'Muzaffarabad City'],
            ['name' => 'Kacheli', 'urdu_name' => 'کچیلی', 'code' => 82231036, 'city' => 'Muzaffarabad City'],
            ['name' => 'Kai Manja', 'urdu_name' => 'کائی منجہ', 'code' => 82231039, 'city' => 'Muzaffarabad City'],
            ['name' => 'Katkair', 'urdu_name' => 'کٹکیر', 'code' => 82231040, 'city' => 'Muzaffarabad City'],
            ['name' => 'Komi Kot', 'urdu_name' => 'کومی کوٹ', 'code' => 82231043, 'city' => 'Muzaffarabad City'],
            ['name' => 'Langerpura', 'urdu_name' => 'لنگر پورہ', 'code' => 82231046, 'city' => 'Muzaffarabad City'],
            ['name' => 'Mera Kalan', 'urdu_name' => 'میرا کلاں', 'code' => 82231054, 'city' => 'Muzaffarabad City'],
            ['name' => 'Municipal Corporation Muzaffarabad', 'urdu_name' => 'میونسپل کارپوریشن مظفرآباد', 'code' => 82232009, 'city' => 'Muzaffarabad City'],
            ['name' => 'Balgran', 'urdu_name' => 'بلگراں', 'code' => 82241003, 'city' => 'Pattika Naseer Abad'],
            ['name' => 'Kahori', 'urdu_name' => 'کہوڑی', 'code' => 82241037, 'city' => 'Pattika Naseer Abad'],
            ['name' => 'Machyara', 'urdu_name' => 'مچھیارہ', 'code' => 82241051, 'city' => 'Pattika Naseer Abad'],
            ['name' => 'Noora Seri', 'urdu_name' => 'نوراسیری', 'code' => 82241061, 'city' => 'Pattika Naseer Abad'],
            ['name' => 'Panjgran', 'urdu_name' => 'پنجگراں', 'code' => 82241063, 'city' => 'Pattika Naseer Abad'],
            ['name' => 'Panjkot', 'urdu_name' => 'پنجکوٹ', 'code' => 82241064, 'city' => 'Pattika Naseer Abad'],
            ['name' => 'Saidpur', 'urdu_name' => 'سید پور', 'code' => 82241066, 'city' => 'Pattika Naseer Abad'],
            ['name' => 'Bheri', 'urdu_name' => 'بھیڑی', 'code' => 82241068, 'city' => 'Pattika Naseer Abad'],
            ['name' => 'Serli Sacha', 'urdu_name' => 'سرلی سچہ', 'code' => 82241069, 'city' => 'Pattika Naseer Abad'],
            ['name' => 'Talgran', 'urdu_name' => 'تلگراں', 'code' => 82241073, 'city' => 'Pattika Naseer Abad'],
            ['name' => 'Chaffar', 'urdu_name' => 'چفاڑ', 'code' => 82311009, 'city' => 'Abbaspur'],
            ['name' => 'Khali Dramun', 'urdu_name' => 'کھلی درمن', 'code' => 82311021, 'city' => 'Abbaspur'],
            ['name' => 'Abbspur', 'urdu_name' => 'عباسپور', 'code' => 82312012, 'city' => 'Abbaspur'],
            ['name' => 'Sehar Kakuta', 'urdu_name' => 'سہرککوٹہ', 'code' => 82321000, 'city' => 'Hajira'],
            ['name' => 'Bhantinee', 'urdu_name' => 'بھانتینی', 'code' => 82321008, 'city' => 'Hajira'],
            ['name' => 'Ghameer', 'urdu_name' => 'گھمیر', 'code' => 82321017, 'city' => 'Hajira'],
            ['name' => 'Battal Mandhol', 'urdu_name' => 'بٹل منڈھول', 'code' => 82321023, 'city' => 'Hajira'],
            ['name' => 'Phagwati', 'urdu_name' => 'پھگواٹی', 'code' => 82321028, 'city' => 'Hajira'],
            ['name' => 'Sarrari', 'urdu_name' => 'سیراڑی', 'code' => 82321033, 'city' => 'Hajira'],
            ['name' => 'Sehra', 'urdu_name' => 'سہڑھ', 'code' => 82321034, 'city' => 'Hajira'],
            ['name' => 'Ali Sojal', 'urdu_name' => 'علی سوجل', 'code' => 82331002, 'city' => 'Rawlakot'],
            ['name' => 'Hussainkot', 'urdu_name' => 'حسین کوٹ', 'code' => 82331003, 'city' => 'Rawlakot'],
            ['name' => 'Banjosa', 'urdu_name' => 'بنجوسہ', 'code' => 82331004, 'city' => 'Rawlakot'],
            ['name' => 'Bangoain', 'urdu_name' => 'بنگوئیں', 'code' => 82331005, 'city' => 'Rawlakot'],
            ['name' => 'Dothan', 'urdu_name' => 'دوتھان', 'code' => 82331016, 'city' => 'Rawlakot'],
            ['name' => 'Hurnamera', 'urdu_name' => 'ہورنہ میرہ', 'code' => 82331019, 'city' => 'Rawlakot'],
            ['name' => 'Jandali', 'urdu_name' => 'جنڈالی', 'code' => 82331020, 'city' => 'Rawlakot'],
            ['name' => 'Pachiot', 'urdu_name' => 'پاچھیوٹ', 'code' => 82331026, 'city' => 'Rawlakot'],
            ['name' => 'Pakher', 'urdu_name' => 'پکھر', 'code' => 82331027, 'city' => 'Rawlakot'],
            ['name' => 'Rehara', 'urdu_name' => 'رہاڑہ', 'code' => 82331032, 'city' => 'Rawlakot'],
            ['name' => 'Singola', 'urdu_name' => 'سنگولہ', 'code' => 82331035, 'city' => 'Rawlakot'],
            ['name' => 'Tain', 'urdu_name' => 'ٹائیں', 'code' => 82331037, 'city' => 'Rawlakot'],
            ['name' => 'Municipal Corporation Rawalakot', 'urdu_name' => 'میونسپل کارپوریشن راولاکوٹ', 'code' => 82332010, 'city' => 'Rawlakot'],
            ['name' => 'Ashkot', 'urdu_name' => 'اشکوٹ', 'code' => 82511004, 'city' => 'Authmuqam'],
            ['name' => 'Barian', 'urdu_name' => 'باڑیاں', 'code' => 82511017, 'city' => 'Authmuqam'],
            ['name' => 'Kundal Shahi', 'urdu_name' => 'کنڈل شاہی', 'code' => 82511023, 'city' => 'Authmuqam'],
            ['name' => 'Neelum', 'urdu_name' => 'نیلم', 'code' => 82511050, 'city' => 'Authmuqam'],
            ['name' => 'Salkhala', 'urdu_name' => 'سالخلہ', 'code' => 82511059, 'city' => 'Authmuqam'],
            ['name' => 'Municipal Committee Authmuqam', 'urdu_name' => 'میونسپل کمیٹی اٹھمقام', 'code' => 82512013, 'city' => 'Authmuqam'],
            ['name' => 'Dodnial', 'urdu_name' => 'دودھنیال', 'code' => 82521025, 'city' => 'Sharda'],
            ['name' => 'Guraiz', 'urdu_name' => 'گریز', 'code' => 82521029, 'city' => 'Sharda'],
            ['name' => 'Kail', 'urdu_name' => 'کیل', 'code' => 82521038, 'city' => 'Sharda'],
            ['name' => 'Sharda UC', 'urdu_name' => 'شاردہ', 'code' => 82521071, 'city' => 'Sharda'],
            ['name' => 'Sangal', 'urdu_name' => 'سانگل', 'code' => 82611001, 'city' => 'Haveli City'],
            ['name' => 'Bhedi', 'urdu_name' => 'بھیڈی', 'code' => 82611003, 'city' => 'Haveli City'],
            ['name' => 'Chanjal', 'urdu_name' => 'چھانجل', 'code' => 82611010, 'city' => 'Haveli City'],
            ['name' => 'Degwar', 'urdu_name' => 'دیگوار', 'code' => 82611013, 'city' => 'Haveli City'],
            ['name' => 'Badhal', 'urdu_name' => 'بدھال', 'code' => 82611021, 'city' => 'Haveli City'],
            ['name' => 'Kalali', 'urdu_name' => 'کلالی', 'code' => 82611024, 'city' => 'Haveli City'],
            ['name' => 'Hillan', 'urdu_name' => 'ہلاں', 'code' => 82621020, 'city' => 'Khurshid Abad'],
            ['name' => 'Kalamula', 'urdu_name' => 'کالا مولہ', 'code' => 82621025, 'city' => 'Khurshid Abad'],
            ['name' => 'Khursheed Abad UC', 'urdu_name' => 'خورشید آباد', 'code' => 82621027, 'city' => 'Khurshid Abad'],
            ['name' => 'Chackhama', 'urdu_name' => 'چکہامہ', 'code' => 82711007, 'city' => 'Hattian'],
            ['name' => 'Sana Daman', 'urdu_name' => 'سینا دامن', 'code' => 82711021, 'city' => 'Hattian'],
            ['name' => 'Gojar Bandi', 'urdu_name' => 'گوجر بانڈی', 'code' => 82711028, 'city' => 'Hattian'],
            ['name' => 'Hattian Bala', 'urdu_name' => 'ہٹیاں بالا', 'code' => 82711030, 'city' => 'Hattian'],
            ['name' => 'Khalana', 'urdu_name' => 'کھلانہ', 'code' => 82711041, 'city' => 'Hattian'],
            ['name' => 'Lamnian', 'urdu_name' => 'لمنیاں', 'code' => 82711045, 'city' => 'Hattian'],
            ['name' => 'Langla', 'urdu_name' => 'لانگلہ', 'code' => 82711047, 'city' => 'Hattian'],
            ['name' => 'Chinari', 'urdu_name' => 'چناری', 'code' => 82711067, 'city' => 'Hattian'],
            ['name' => 'Bana Mola', 'urdu_name' => 'بنہ مولہ', 'code' => 82721057, 'city' => 'Leepa'],
            ['name' => 'Nokot', 'urdu_name' => 'نوکوٹ', 'code' => 82721062, 'city' => 'Leepa'],
            ['name' => 'Bagh UC', 'urdu_name' => 'باغ', 'code' => 82111002, 'city' => 'Bagh City'],
            ['name' => 'Islam Nagar', 'urdu_name' => 'اسلام نگر', 'code' => 82111004, 'city' => 'Bagh City'],
            ['name' => 'Birpani', 'urdu_name' => 'بیر پانی', 'code' => 82111005, 'city' => 'Bagh City'],
            ['name' => 'Dharay', 'urdu_name' => 'دھڑے', 'code' => 82111014, 'city' => 'Bagh City'],
            ['name' => 'Juglari', 'urdu_name' => 'جگلڑی', 'code' => 82111022, 'city' => 'Bagh City'],
            ['name' => 'Nar Sher Ali Khan', 'urdu_name' => 'ناڑ شیر علی خان', 'code' => 82111030, 'city' => 'Bagh City'],
            ['name' => 'Rawali', 'urdu_name' => 'راولی', 'code' => 82111032, 'city' => 'Bagh City'],
            ['name' => 'Swanj', 'urdu_name' => 'سوانج', 'code' => 82111035, 'city' => 'Bagh City'],
            ['name' => 'Thub', 'urdu_name' => 'تھب', 'code' => 82111036, 'city' => 'Bagh City'],
            ['name' => 'Topi', 'urdu_name' => 'ٹوپی', 'code' => 82111037, 'city' => 'Bagh City'],
            ['name' => 'Bani Passari', 'urdu_name' => 'بنی پساری', 'code' => 82111039, 'city' => 'Bagh City'],
            ['name' => 'Chammyati', 'urdu_name' => 'چمیاٹی', 'code' => 82121007, 'city' => 'Dhirkot'],
            ['name' => 'Chirala', 'urdu_name' => 'چڑالہ', 'code' => 82121011, 'city' => 'Dhirkot'],
            ['name' => 'Mallot', 'urdu_name' => 'ملوٹ', 'code' => 82121040, 'city' => 'Dhirkot'],
        ];

        // ======================
        // TEHSILS OF AJK
        // ======================

        $tehsils = [

            // Muzaffarabad District
            ['name' => 'Muzaffarabad', 'urdu_name' => 'مظفرآباد', 'code' => 8223, 'district' => 'Muzaffarabad'],
            ['name' => 'Pattika', 'urdu_name' => 'پٹہکہ', 'code' => 8224, 'district' => 'Muzaffarabad'],
            ['name' => 'Chikar', 'urdu_name' => 'چکار', 'code' => null, 'district' => 'Muzaffarabad'],

            // Neelam District
            ['name' => 'Athmuqam', 'urdu_name' => 'اٹھمقام', 'code' => 8251, 'district' => 'Neelam'],
            ['name' => 'Sharda', 'urdu_name' => 'شاردہ', 'code' => null, 'district' => 'Neelam'],
            ['name' => 'Kel', 'urdu_name' => 'کیل', 'code' => null, 'district' => 'Neelam'],
            ['name' => 'Leepa', 'urdu_name' => 'لیپہ', 'code' => null, 'district' => 'Neelam'],

            // Haveli District
            ['name' => 'Forward Kahuta', 'urdu_name' => 'فارورڈ کہوٹہ', 'code' => 8261, 'district' => 'Haveli'],

            // Mirpur District
            ['name' => 'Mirpur', 'urdu_name' => 'میرپور', 'code' => 8132, 'district' => 'Mirpur'],
            ['name' => 'Dadyal', 'urdu_name' => 'ڈڈیال', 'code' => null, 'district' => 'Mirpur'],
            ['name' => 'Chakswari', 'urdu_name' => 'چکسواری', 'code' => null, 'district' => 'Mirpur'],
            ['name' => 'Islamgarh', 'urdu_name' => 'اسلام گڑھ', 'code' => null, 'district' => 'Mirpur'],

            // Kotli District
            ['name' => 'Kotli', 'urdu_name' => 'کوٹلی', 'code' => 8122, 'district' => 'Kotli'],
            ['name' => 'Sehnsa', 'urdu_name' => 'سہنسہ', 'code' => null, 'district' => 'Kotli'],
            ['name' => 'Fatehpur Thakiala', 'urdu_name' => 'فتح پور تھکیالہ', 'code' => 8121, 'district' => 'Kotli'],
            ['name' => 'Khuiratta', 'urdu_name' => 'کھوئی رٹہ', 'code' => 8125, 'district' => 'Kotli'],
            ['name' => 'Charoi', 'urdu_name' => 'چڑہوئی', 'code' => null, 'district' => 'Kotli'],

            // Bhimber District
            ['name' => 'Bhimber', 'urdu_name' => 'بھمبر', 'code' => 8112, 'district' => 'Bhimber'],
            ['name' => 'Barnala', 'urdu_name' => 'برنالہ', 'code' => null, 'district' => 'Bhimber'],
            ['name' => 'Samahni', 'urdu_name' => 'سماہنی', 'code' => null, 'district' => 'Bhimber'],
            ['name' => 'Dullian Jattan', 'urdu_name' => 'دلیاں جٹاں', 'code' => 8126, 'district' => 'Bhimber'],

            // Poonch District
            ['name' => 'Rawalakot', 'urdu_name' => 'راولاکوٹ', 'code' => 8233, 'district' => 'Poonch'],
            ['name' => 'Hajira', 'urdu_name' => 'ہجیرہ', 'code' => 8232, 'district' => 'Poonch'],
            ['name' => 'Abbaspur', 'urdu_name' => 'عباسپور', 'code' => 8231, 'district' => 'Poonch'],
            ['name' => 'Thorar', 'urdu_name' => 'تھوراڑ', 'code' => null, 'district' => 'Poonch'],

            // Bagh District
            ['name' => 'Bagh', 'urdu_name' => 'باغ', 'code' => null, 'district' => 'Bagh'],
            ['name' => 'Dhirkot', 'urdu_name' => 'دھیرکوٹ', 'code' => null, 'district' => 'Bagh'],
            ['name' => 'Hari Ghel', 'urdu_name' => 'ہری گھیل', 'code' => null, 'district' => 'Bagh'],

            // Sudhanoti District
            ['name' => 'Pallandri', 'urdu_name' => 'پلندری', 'code' => 8241, 'district' => 'Sudhanoti'],
            ['name' => 'Mang', 'urdu_name' => 'منگ', 'code' => null, 'district' => 'Sudhanoti'],
            ['name' => 'Tarar Khal', 'urdu_name' => 'تراڑکھل', 'code' => 8242, 'district' => 'Sudhanoti'],

            // Jhelum Valley District
            ['name' => 'Hattian Bala', 'urdu_name' => 'ہٹیاں بالا', 'code' => 8271, 'district' => 'Jehlum Valley'],
            ['name' => 'Chikar', 'urdu_name' => 'چکار', 'code' => null, 'district' => 'Jehlum Valley'],
        ];


        foreach ($tehsils as $tehsil) {

            Demography::create([
                'name' => $tehsil['name'],
                'urdu_name' => $tehsil['urdu_name'],
                'type' => 'TEHSIL',
                'parent_id' => $districtIds[$tehsil['district']],
                'is_ajk_district' => 1,
                'code' => $tehsil['code']
            ]);
        }




        // districts of Pakistan 
        //         $districts = [
        //             ['name' => 'Mirpur', 'urdu_name' => 'میرپور', 'code' => 813],
        //             ['name' => 'Kotli', 'urdu_name' => 'کوٹلی', 'code' => 812],
        //             ['name' => 'Bhimber', 'urdu_name' => 'بھمبر', 'code' => 811],
        //             ['name' => 'Muzaffarabad', 'urdu_name' => 'مظفرآباد', 'code' => 822],
        //             ['name' => 'Jehlum Valley', 'urdu_name' => 'جہلم ویلی', 'code' => 827],
        //             ['name' => 'Neelam', 'urdu_name' => 'نیلم', 'code' => 825],
        //             ['name' => 'Poonch', 'urdu_name' => 'پونچھ', 'code' => 823],
        //             ['name' => 'Haveli', 'urdu_name' => 'حویلی', 'code' => 826],
        //             ['name' => 'Bagh', 'urdu_name' => 'باغ', 'code' => 821],
        //             ['name' => 'Sudhanoti', 'urdu_name' => 'سدھنوتی', 'code' => 824],
        //              ['name' => 'Kupwara', 'urdu_name' => 'کپواڑہ', 'code' => 0],
        //             ['name' => 'Jammu', 'urdu_name' => 'جموں', 'code' => 0],
        //             ['name' => 'Baramulla', 'urdu_name' => 'بارامولا', 'code' => 0],
        //             ['name' => 'Riasi', 'urdu_name' => 'ریاسی', 'code' => 0],
        //             ['name' => 'Anantnag', 'urdu_name' => 'اننت ناگ', 'code' => 0],
        //             ['name' => 'Banihal', 'urdu_name' => 'بانہال', 'code' => 0],
        //             ['name' => 'Pulwama', 'urdu_name' => 'پلوامہ', 'code' => 0],
        //             ['name' => 'Samba', 'urdu_name' => 'سمبل', 'code' => 0],
        //             ['name' => 'Rajouri', 'urdu_name' => 'راجوری', 'code' => 0],
        //             ['name' => 'Srinagar', 'urdu_name' => 'سرینگر', 'code' => 0],
        //             ['name' => 'Abbottabad', 'urdu_name' => 'ایبٹ آباد (پاکستان)', 'code' => 0],
        //             ['name' => 'Bandipora', 'urdu_name' => 'بانڈی پورہ', 'code' => 0],
        //             ['name' => 'Islamabad', 'urdu_name' => 'اسلام آباد', 'code' => 0],
        //             ['name' => 'Doda', 'urdu_name' => 'ڈوڈہ', 'code' => 0],
        //             ['name' => 'Udhampur', 'urdu_name' => 'ادھم پورہ', 'code' => 0],
        //             ['name' => 'Budgam', 'urdu_name' => 'بڈگام', 'code' => 0],
        //             ['name' => 'Sialkot', 'urdu_name' => 'سیالکوٹ (پاکستان)', 'code' => 0],
        //              ['name' => 'Mansehra', 'urdu_name' => 'مانسہرہ (پاکستان)', 'code' => 0],
        //             ['name' => 'Kishtwar', 'urdu_name' => 'کشتواڑ', 'code' => 0],
        //             ['name' => 'Kathua', 'urdu_name' => 'کٹھوعہ', 'code' => 0],
        //             ['name' => 'Lahore', 'urdu_name' => 'لاہور', 'code' => 0],
        //             ['name' => 'Kargil', 'urdu_name' => 'کرگل', 'code' => 0],
        //             ['name' => 'Ladakh', 'urdu_name' => 'لداخ', 'code' => 0],
        //             ['name' => 'Attock', 'urdu_name' => 'اٹک', 'code' => 0],
        //             ['name' => 'Haripur', 'urdu_name' => 'ہری پور', 'code' => 0],
        //             ['name' => 'Udhampur', 'urdu_name' => 'ادھمپور', 'code' => 0],
        //             ['name' => 'Diamer', 'urdu_name' => 'دیامر (گلگت)', 'code' => 0],
        //             ['name' => 'Pallandri', 'urdu_name' => 'پلندری', 'code' => 0],
        //             ['name' => 'Kulgam', 'urdu_name' => 'کلگام', 'code' => 0],
        //             ['name' => 'Charsadda', 'urdu_name' => 'چارسدہ (پاکستان)', 'code' => 0],
        //             ['name' => 'Kohat', 'urdu_name' => 'کوہاٹ', 'code' => 0],
        //             ['name' => 'Buner', 'urdu_name' => 'بونیر', 'code' => 0],
        //             ['name' => 'Uri', 'urdu_name' => 'اوڑی', 'code' => 0],
        //             ['name' => 'Handwara', 'urdu_name' => 'ہندواڑہ', 'code' => 0],
        //             ['name' => 'Sopore', 'urdu_name' => 'سوپور', 'code' => 0],
        //             ['name' => 'Tetwal', 'urdu_name' => 'ٹیٹوال', 'code' => 0],
        //             ['name' => 'Baramula', 'urdu_name' => 'بارہ مولہ', 'code' => 0],


        // ];
        //             foreach ( $districts as $district) {
        //                 Demography::create([
        //                     'name' => $district['name'],
        //                     'urdu_name' => $district['urdu_name'],
        //                     'type' => 'DISTRICT',
        //                     'parent_id' => Demography::where('name', 'Pakistan')->first()->id,
        //                     'is_ajk_district' => in_array($district['name'], ['Mirpur', 'Kotli', 'Bhimber', 'Muzaffarabad', 'Jehlum Valley', 'Neelam', 'Poonch', 'Haveli', 'Bagh', 'Sudhanoti']) ? true : false,
        //                 'code' => $district['code']
        //             ]);
        //         }



        // Tehsils

        // $tehsils = [
        //     ['name' => 'Muzzafarabad', 'urdu_name' => 'مظفرآباد', 'code' => 8223, 'parent_id' => $districtajk[1]['id']],
        //     ['name' => 'Haveli', 'urdu_name' => 'حویلی', 'code' => 8261, 'parent_id' => $districtajk[5]['id']],

        //     ['name' => 'Poonch', 'urdu_name' => 'پونچھ', 'code' => null, 	'parent_id' => $districtajk[6]['id']],
        //     ['name' => 'Sudhanoti', 'urdu_name' => 'سدھنوتی', 'code' => null, 'parent_id' => $districtajk[4]['id']],
        //     ['name' => 'Bagh', 'urdu_name' => 'باغ', 'code' => null, 'parent_id' => $district[2]['id']],

        //     ['name' => 'Mirpur', 'urdu_name' => 'میرپور', 'code' => 8132, 'parent_id' => $districtajk[0]['id']],
        //     ['name' => 'Chackswari', 'urdu_name' => 'چکسواری', 'code' => null, 'parent_id' => $district[21]['id']],
        //     ['name' => 'Dadyal', 'urdu_name' => 'ڈڈیال', 'code' => null, 'parent_id' => $district[20]['id']],



        //     ['name' => 'Palandari', 'urdu_name' => 'پلندری', 'code' => 8241, 'parent_id' => $districtajk[2]['id']],
        //     ['name' => 'Kotli', 'urdu_name' => 'کوٹلی', 'code' => 8122, 'parent_id' => $districtajk[3]['id']],

        //     ['name' => 'Rawlakot', 'urdu_name' => 'راولاکوٹ', 'code' => 8233, 'parent_id' => $districtajk[10]['id']],
        //     ['name' => 'Thorar', 'urdu_name' => 'تھوراڑ', 'code' => null, 'parent_id' => $district[32]['id']],

        //     ['name' => 'Hattian', 'urdu_name' => 'ہٹیاں', 'code' => 8271, 'parent_id' => $districtajk[7]['id']],
        //     ['name' => 'Abbaspur', 'urdu_name' => 'عباس پور', 'code' => 8231, 'parent_id' => $districtajk[8]['id']],
        //     ['name' => 'Hajira', 'urdu_name' => 'ہجیرہ', 'code' => 8232, 'parent_id' => $districtajk[9]['id']],
        //     ['name' => 'Pattika Naseer Abad', 'urdu_name' => 'پٹہکہ نصیرآباد ', 'code' => 8224, 'parent_id' => $districtajk[11]['id']],
        //     ['name' => 'Khuiratta', 'urdu_name' => 'کھوئی رٹہ', 'code' => 8125, 'parent_id' => $districtajk[12]['id']],
        //     ['name' => 'Fatehpur Thakiala-Nakyal', 'urdu_name' => 'فتح پور تھکیال', 'code' => 8121 , 'parent_id' => $districtajk[13]['id']],
        //     ['name' => 'DULLIA JATTAN', 'urdu_name' => 'دلیا جٹاں', 'code' => 8126, 'parent_id' => $districtajk[14]['id']],
        //     ['name' => 'BHIMBER', 'urdu_name' => 'بھمبر', 'code' => 8112, 'parent_id' => $districtajk[15]['id']],
        //     ['name' => 'NotSet', 'urdu_name' => 'کپواڑہ', 'code' => 0, 'parent_id' => $districtajk[16]['id']],
        //     ['name' => 'NotSet', 'urdu_name' => 'جموں', 'code' => 0, 'parent_id' => $districtajk[17]['id']],
        //     ['name' => 'Sehnsa', 'urdu_name' => 'سہنسہ', 'code' => null, 'parent_id' => $district[18]['id']],
        //     ['name' => 'Charoi', 'urdu_name' => 'چڑہوئی', 'code' => null, 'parent_id' => $district[19]['id']],
        //     ['name' => 'Barnala', 'urdu_name' => 'برنالہ', 'code' => null, 'parent_id' => $district[22]['id']],
        //     ['name' => 'Samahni', 'urdu_name' => 'سماہنی', 'code' => null, 	'parent_id' => $district[23]['id']],
        //     ['name' => 'Chikkar', 'urdu_name' => 'چکار', 'code' => null, 'parent_id' => $district[24]['id']],
        //     ['name' => 'Leepa', 'urdu_name' => 'لیپہ', 'code' => null, 'parent_id' => $district[25]['id']],
        //     ['name' => 'Authmuqam', 'urdu_name' => 'اٹھمقام', 'code' => 8251, 'parent_id' => $district[26]['id']],
        //     ['name' => 'Sharda', 'urdu_name' => 'شاردہ', 'code' => null, 'parent_id' => $district[27]['id']],
        //     ['name' => 'Dhirkot', 'urdu_name' => 'دھیرکوٹ', 'code' => null, 'parent_id' => $district[28]['id']],
        //     ['name' => 'Hari Ghel', 'urdu_name' => 'ہری گھیل', 'code' => null, 	'parent_id' => $district[29]['id']],
        //     ['name' => 'Khurshid Abad', 'urdu_name' => 'خورشید ٓباد', 'code' => null, 'parent_id' => $district[30]['id']],
        //     ['name' => 'Mumtazabad', 'urdu_name' => 'ممتاز ٓباد', 'code' => null, 'parent_id' => $district[31]['id']],
        //     ['name' => 'Baloch', 'urdu_name' => 'بلوچ', 'code' => null, 'parent_id' => $district[33]['id']],
        //     ['name' => 'Mang', 'urdu_name' => 'منگ', 'code' => null, 'parent_id' => $district[34]['id']],
        //     ['name' => 'Pallandri', 'urdu_name' => 'پلندری', 'code' => null, 	'parent_id' => $district[35]['id']],
        //     ['name' => 'Tarar Khal', 'urdu_name' => 'تراڑکھل', 'code' => 8242, 'parent_id' => $district[36]['id']],
        //     ['name' => 'DULLIA JATTAN', 'urdu_name' => 'دلیا جٹاں', 'code' => 8126, 'parent_id' => $district[37]['id']],
        //     ['name' => 'NotSet', 'urdu_name' => 'کپواڑہ', 'code' => 0, 'parent_id' => $district[39]['id']],
        //     ['name' => 'NotSet', 'urdu_name' => 'جموں', 'code' => 0, 'parent_id' => $district[40]['id']]
        // ];

        //     foreach ( $tehsils as $tehsil) {
        //                     Demography::create([
        //                         'name' => $tehsil['name'],
        //                         'urdu_name' => $tehsil['urdu_name'],
        //                         'type' => 'TEHSIL',
        //                         'parent_id' => $tehsil['parent_id'],
        //                         // 'is_ajk_district' => true,
        //                         'code' => $tehsil['code']
        //                     ]);
        //              }



        //Cities

        $cities = [
            ['name' => 'Mirpur', 'urdu_name' => 'میرپور', 'code' => 8132],
            ['name' => 'Muzzafarabad', 'urdu_name' => 'مظفرآباد', 'code' => 8223],
            ['name' => 'Palandari', 'urdu_name' => 'پلندری', 'code' => 8241],
            ['name' => 'Kotli', 'urdu_name' => 'کوٹلی', 'code' => 8122],
            ['name' => 'Sudhanoti', 'urdu_name' => 'سدھنوتی', 'code' => null],
            ['name' => 'Haveli', 'urdu_name' => 'حویلی', 'code' => 8261],
            ['name' => 'Poonch', 'urdu_name' => 'پونچھ', 'code' => null],
            ['name' => 'Hattian', 'urdu_name' => 'ہٹیاں', 'code' => 8271],
            ['name' => 'Abbaspur', 'urdu_name' => 'عباس پور', 'code' => 8231],
            ['name' => 'Hajira', 'urdu_name' => 'ہجیرہ', 'code' => 8232],
            ['name' => 'Rawlakot', 'urdu_name' => 'راولاکوٹ', 'code' => 8233],
            ['name' => 'Pattika Naseer Abad', 'urdu_name' => 'پٹہکہ نصیرآباد ', 'code' => 8224],
            ['name' => 'Khuiratta', 'urdu_name' => 'کھوئی رٹہ', 'code' => 8125],
            ['name' => 'Fatehpur Thakiala-Nakyal', 'urdu_name' => 'فتح پور تھکیال', 'code' => 8121],
            ['name' => 'Sehnsa', 'urdu_name' => 'سہنسہ', 'code' => null],
            ['name' => 'Charoi', 'urdu_name' => 'چڑہوئی', 'code' => null],
            ['name' => 'Dadyal', 'urdu_name' => 'ڈڈیال', 'code' => null],
            ['name' => 'Chackswari', 'urdu_name' => 'چکسواری', 'code' => null],
            ['name' => 'Barnala', 'urdu_name' => 'برنالہ', 'code' => null],
            ['name' => 'Samahni', 'urdu_name' => 'سماہنی', 'code' => null],
            ['name' => 'Chikkar', 'urdu_name' => 'چکار', 'code' => null],
            ['name' => 'Leepa', 'urdu_name' => 'لیپہ', 'code' => null],
            ['name' => 'Authmuqam', 'urdu_name' => 'اٹھمقام', 'code' => 8251],
            ['name' => 'Sharda', 'urdu_name' => 'شاردہ', 'code' => null],
            ['name' => 'Dhirkot', 'urdu_name' => 'دھیرکوٹ', 'code' => null],
            ['name' => 'Hari Ghel', 'urdu_name' => 'ہری گھیل', 'code' => null],
            ['name' => 'Bagh', 'urdu_name' => 'باغ', 'code' => null],
            ['name' => 'Khurshid Abad', 'urdu_name' => 'خورشید ٓباد', 'code' => null],
            ['name' => 'Mumtazabad', 'urdu_name' => 'ممتاز ٓباد', 'code' => null],
            ['name' => 'Thorar', 'urdu_name' => 'تھوراڑ', 'code' => null],
            ['name' => 'Baloch', 'urdu_name' => 'بلوچ', 'code' => null],
            ['name' => 'Mang', 'urdu_name' => 'منگ', 'code' => null],
            ['name' => 'Pallandri', 'urdu_name' => 'پلندری', 'code' => null],
            ['name' => 'Tarar Khal', 'urdu_name' => 'تراڑکھل', 'code' => 8242],
            ['name' => 'DULLIA JATTAN', 'urdu_name' => 'دلیا جٹاں', 'code' => 8126],
            ['name' => 'BHIMBER', 'urdu_name' => 'بھمبر', 'code' => 8112],
            ['name' => 'NotSet', 'urdu_name' => 'کپواڑہ', 'code' => 0],
        ];

        // foreach ( $cities as $city) {
        //                 Demography::create([
        //                     'name' => $city['name'],
        //                     'urdu_name' => $city['urdu_name'],
        //                     'type' => 'CITY',
        //                     // 'parent_id' => Demography::where('name', $city['district'])->first()->id,
        //                     // 'is_ajk_district' => true,
        //                     'code' => $city['code']
        //                 ]);
        //          }

        $tehsilCodes = Demography::where('type', 'TEHSIL')
            ->pluck('id', 'code');

        foreach ($cities as $city) {

            $parentId = null;

            if (!empty($city['code']) && $city['code'] != 0) {
                $parentId = $tehsilCodes[$city['code']] ?? null;
            }

            Demography::create([
                'name' => $city['name'],
                'urdu_name' => $city['urdu_name'],
                'type' => 'CITY',
                'parent_id' => $parentId,
                'code' => $city['code']
            ]);
        }


        //Union Councils


        $union_councils = [
            ['name' => 'Ankar', 'urdu_name' => 'انکر', 'code' => 81311003],
            ['name' => 'Chatroh', 'urdu_name' => 'چھتروہ', 'code' => 81311019],
            ['name' => 'Kathar', 'urdu_name' => 'کٹھاڑ', 'code' => 81311031],
            ['name' => 'Onah', 'urdu_name' => 'اوناع', 'code' => 81311042],
            ['name' => 'Raipur', 'urdu_name' => 'رائے پور', 'code' => 81311050],
            ['name' => 'Ratta', 'urdu_name' => 'رٹہ', 'code' => 81311052],
            ['name' => 'Siakh', 'urdu_name' => 'سیاکھ', 'code' => 81311056],
            ['name' => 'Municipal Committee Dadyal', 'urdu_name' => 'میونسپل کمیٹی ڈڈیال', 'code' => 81312018],
            ['name' => 'Afzalpur', 'urdu_name' => 'افضل پور', 'code' => 81321001],
            ['name' => 'Dhangri Bala', 'urdu_name' => 'ڈھانگری بالا', 'code' => 81321012],
            ['name' => 'Municipal Committee Chaksawari (CHAK SAWARI)', 'urdu_name' => 'میونسپل کمیٹی چکسواری', 'code' => 81321015],
            ['name' => 'Kaneli', 'urdu_name' => 'کنیلی', 'code' => 81321030],
            ['name' => 'Kharak', 'urdu_name' => 'کھاڑک', 'code' => 81321032],
            ['name' => 'Khari Khas', 'urdu_name' => 'کھڑی خاص', 'code' => 81321034],
            ['name' => 'Naugran', 'urdu_name' => 'نوگراں', 'code' => 81321041],
            ['name' => 'Panyam', 'urdu_name' => 'پنیام', 'code' => 81321045],
            ['name' => 'Pind Khurd', 'urdu_name' => 'پنڈخورد', 'code' => 81321046],
            ['name' => 'Pindi Sabharwal', 'urdu_name' => 'پنڈی سبھروال', 'code' => 81321047],
            ['name' => 'Potha Bainsi', 'urdu_name' => 'پوٹھہ بینسی', 'code' => 81321049],
            ['name' => 'Rathoa Muhammad Ali', 'urdu_name' => 'رٹھوعہ محمد علی', 'code' => 81321051],
            ['name' => 'Samwal Sharif', 'urdu_name' => 'سموال شریف', 'code' => 81321055],
            ['name' => 'Municipal Corporation Mirpur', 'urdu_name' => 'میونسپل کارپوریشن میرپور', 'code' => 81322016],
            ['name' => 'Municipal Committee Islamgarh', 'urdu_name' => 'میونسپل کمیٹی اسلام گڑھ', 'code' => 81322017],
            ['name' => 'Tharian (Chanal Bung)', 'urdu_name' => 'ٹھیریاں', 'code' => 82231008],
            ['name' => 'Chatter Domail', 'urdu_name' => 'چھتر دومیل', 'code' => 82231015],
            ['name' => 'Charakpura', 'urdu_name' => 'چڑکپورہ', 'code' => 82231018],
            ['name' => 'Chatter Kalas', 'urdu_name' => 'چھتر کلاس', 'code' => 82231019],
            ['name' => 'Danna', 'urdu_name' => 'ڈنہ', 'code' => 82231022],
            ['name' => 'Gojra', 'urdu_name' => 'گوجرہ', 'code' => 82231027],
            ['name' => 'Hattian Dupatta', 'urdu_name' => 'ہٹیاں دوپٹہ', 'code' => 82231031],
            ['name' => 'Heer Kotli', 'urdu_name' => 'ہیر کوٹلی', 'code' => 82231032],
            ['name' => 'Jhandgran', 'urdu_name' => 'جھنڈگراں', 'code' => 82231035],
            ['name' => 'Kacheli', 'urdu_name' => 'کچیلی', 'code' => 82231036],
            ['name' => 'Kai Manja', 'urdu_name' => 'کائی منجہ', 'code' => 82231039],
            ['name' => 'Katkair', 'urdu_name' => 'کٹکیر', 'code' => 82231040],
            ['name' => 'Komi Kot', 'urdu_name' => 'کومی کوٹ', 'code' => 82231043],
            ['name' => 'Langerpura', 'urdu_name' => 'لنگر پورہ', 'code' => 82231046],
            ['name' => 'Mera Kalan', 'urdu_name' => 'میرا کلاں', 'code' => 82231054],
            ['name' => 'Muzaffarabad', 'urdu_name' => 'مظفرآباد', 'code' => 82231058],
            ['name' => 'Municipal Corporation Muzaffarabad', 'urdu_name' => 'میونسپل کارپوریشن مظفرآباد', 'code' => 82232009],
            ['name' => 'Balgran', 'urdu_name' => 'بلگراں', 'code' => 82241003],
            ['name' => 'Kahori', 'urdu_name' => 'کہوڑی', 'code' => 82241037],
            ['name' => 'Machyara', 'urdu_name' => 'مچھیارہ', 'code' => 82241051],
            ['name' => 'Noora Seri', 'urdu_name' => 'نوراسیری', 'code' => 82241061],
            ['name' => 'Panjgran', 'urdu_name' => 'پنجگراں', 'code' => 82241063],
            ['name' => 'Panjkot', 'urdu_name' => 'پنجکوٹ', 'code' => 82241064],
            ['name' => 'Saidpur', 'urdu_name' => 'سید پور', 'code' => 82241066],
            ['name' => 'Bheri (SERI BHERI)', 'urdu_name' => 'بھیڑی', 'code' => 82241068],
            ['name' => 'Serli Sacha (Sachian)', 'urdu_name' => 'سرلی سچہ', 'code' => 82241069],
            ['name' => 'Talgran', 'urdu_name' => 'تلگراں', 'code' => 82241073],
            ['name' => 'Chaffar', 'urdu_name' => 'چفاڑ', 'code' => 82311009],
            ['name' => 'Khali Dramun', 'urdu_name' => 'کھلی درمن', 'code' => 82311021],
            ['name' => 'Abbspur', 'urdu_name' => 'عباسپور', 'code' => 82312012],
            ['name' => 'Sehar Kakuta', 'urdu_name' => 'سہرککوٹہ', 'code' => 82321000],
            ['name' => 'Bhantinee', 'urdu_name' => 'بھانتینی', 'code' => 82321008],
            ['name' => 'Ghameer(GHAMBIR)', 'urdu_name' => 'گھمیر', 'code' => 82321017],
            ['name' => 'Battal Mandhol(MENDHOLE)', 'urdu_name' => 'بٹل منڈھول', 'code' => 82321023],
            ['name' => 'Phagwati', 'urdu_name' => 'پھگواٹی', 'code' => 82321028],
            ['name' => 'Sarrari', 'urdu_name' => 'سیراڑی', 'code' => 82321033],
            ['name' => 'Sehra', 'urdu_name' => 'سہڑھ', 'code' => 82321034],
            ['name' => 'Ali Sojal', 'urdu_name' => 'علی سوجل', 'code' => 82331002],
            ['name' => 'Hussainkot (Azizabad)', 'urdu_name' => 'حسین کوٹ', 'code' => 82331003],
            ['name' => 'Banjosa', 'urdu_name' => 'بنجوسہ', 'code' => 82331004],
            ['name' => 'Bangoain', 'urdu_name' => 'بنگوئیں', 'code' => 82331005],
            ['name' => 'Dothan', 'urdu_name' => 'دوتھان', 'code' => 82331016],
            ['name' => 'Hurnamera', 'urdu_name' => 'ہورنہ میرہ', 'code' => 82331019],
            ['name' => 'Jandali', 'urdu_name' => 'جنڈالی', 'code' => 82331020],
            ['name' => 'Pachiot', 'urdu_name' => 'پاچھیوٹ', 'code' => 82331026],
            ['name' => 'Pakher', 'urdu_name' => 'پکھر', 'code' => 82331027],
            ['name' => 'Rehara', 'urdu_name' => 'رہاڑہ', 'code' => 82331032],
            ['name' => 'Singola', 'urdu_name' => 'سنگولہ', 'code' => 82331035],
            ['name' => 'Tain', 'urdu_name' => 'ٹائیں', 'code' => 82331037],
            ['name' => 'Municipal Corporation Rawalakot', 'urdu_name' => 'میونسپل کارپوریشن راولاکوٹ', 'code' => 82332010],
            ['name' => 'Thorar', 'urdu_name' => 'تھوراڑ', 'code' => 82341045],
            ['name' => 'Baral', 'urdu_name' => 'بارل', 'code' => 82411003],
            ['name' => 'Dhar Dharach', 'urdu_name' => 'دہاردہرچھ', 'code' => 82411007],
            ['name' => 'Gorah', 'urdu_name' => 'گوراہ', 'code' => 82411009],
            ['name' => 'Jhanda Bagla', 'urdu_name' => 'جھنڈا بگلہ', 'code' => 82411013],
            ['name' => 'Papay Nar(Panthal)', 'urdu_name' => 'پپے ناڑ', 'code' => 82411018],
            ['name' => 'Town Pallandri', 'urdu_name' => 'ٹاؤن پلندری', 'code' => 82412005],
            ['name' => 'Nerian', 'urdu_name' => 'نیریاں', 'code' => 82421016],
            ['name' => 'Basari', 'urdu_name' => 'بساڑی', 'code' => 82431004],
            ['name' => 'Chowkian', 'urdu_name' => 'چوکیاں', 'code' => 82431006],
            ['name' => 'Khala', 'urdu_name' => 'کہالہ', 'code' => 82431014],
            ['name' => 'Mong', 'urdu_name' => 'منگ', 'code' => 82441015],
            ['name' => 'Patten Sher Khan', 'urdu_name' => 'پتن شیر خان', 'code' => 82441020],
            ['name' => 'Ashkot', 'urdu_name' => 'اشکوٹ', 'code' => 82511004],
            ['name' => 'Barian (Barian-1)', 'urdu_name' => 'باڑیاں', 'code' => 82511017],
            ['name' => 'Kundal Shahi (Neelum-3)', 'urdu_name' => 'کنڈل شاہی', 'code' => 82511023],
            ['name' => 'Neelum (Neelum-1)', 'urdu_name' => 'نیلم', 'code' => 82511050],
            ['name' => 'Salkhala (Neelum-2)', 'urdu_name' => 'سالخلہ', 'code' => 82511059],
            ['name' => 'Municipal Committee Authmuqam', 'urdu_name' => 'میونسپل کمیٹی اٹھمقام', 'code' => 82512013],
            ['name' => 'Dodnial', 'urdu_name' => 'دودھنیال', 'code' => 82521025],
            ['name' => 'Guraiz', 'urdu_name' => 'گریز', 'code' => 82521029],
            ['name' => 'Kail', 'urdu_name' => 'کیل', 'code' => 82521038],
            ['name' => 'Sharda', 'urdu_name' => 'شاردہ', 'code' => 82521071],
            ['name' => 'Sangal (Agiwas)', 'urdu_name' => 'سانگل', 'code' => 82611001],
            ['name' => 'Bhedi', 'urdu_name' => 'بھیڈی', 'code' => 82611003],
            ['name' => 'Chanjal', 'urdu_name' => 'چھانجل', 'code' => 82611010],
            ['name' => 'Degwar', 'urdu_name' => 'دیگوار', 'code' => 82611013],
            ['name' => 'Badhal (Jabian)', 'urdu_name' => 'بدھال', 'code' => 82611021],
            ['name' => 'Kalali', 'urdu_name' => 'کلالی', 'code' => 82611024],
            ['name' => 'Town Committee Kahutta', 'urdu_name' => 'ٹاون کمیٹی کہوٹہ', 'code' => 82612009],
            ['name' => 'Hillan', 'urdu_name' => 'ہلاں', 'code' => 82621020],
            ['name' => 'Kalamula', 'urdu_name' => 'کالا مولہ', 'code' => 82621025],
            ['name' => 'Khursheed Abad', 'urdu_name' => 'خورشید آباد', 'code' => 82621027],
            ['name' => 'Chackhama', 'urdu_name' => 'چکہامہ', 'code' => 82711007],
            ['name' => 'Sana Daman (Daman)', 'urdu_name' => 'سینا دامن', 'code' => 82711021],
            ['name' => 'Gojar Bandi', 'urdu_name' => 'گوجر بانڈی', 'code' => 82711028],
            ['name' => 'Hattian Bala', 'urdu_name' => 'ہٹیاں بالا', 'code' => 82711030],
            ['name' => 'Khalana', 'urdu_name' => 'کھلانہ', 'code' => 82711041],
            ['name' => 'Lamnian', 'urdu_name' => 'لمنیاں', 'code' => 82711045],
            ['name' => 'Langla', 'urdu_name' => 'لانگلہ', 'code' => 82711047],
            ['name' => 'Chinari(Sarak Chinari)', 'urdu_name' => 'چناری', 'code' => 82711067],
            ['name' => 'Bana Mola', 'urdu_name' => 'بنہ مولہ', 'code' => 82721057],
            ['name' => 'Nokot', 'urdu_name' => 'نوکوٹ', 'code' => 82721062],
            ['name' => 'Chikar', 'urdu_name' => 'چکار', 'code' => 82731016],
            ['name' => 'Salmia', 'urdu_name' => 'سلمیہ', 'code' => 82731072],
            ['name' => 'Bagh', 'urdu_name' => 'باغ', 'code' => 82111002],
            ['name' => 'Islam Nagar', 'urdu_name' => 'اسلام نگر', 'code' => 82111004],
            ['name' => 'Birpani', 'urdu_name' => 'بیر پانی', 'code' => 82111005],
            ['name' => 'Dharay', 'urdu_name' => 'دھڑے', 'code' => 82111014],
            ['name' => 'Juglari', 'urdu_name' => 'جگلڑی', 'code' => 82111022],
            ['name' => 'Nar Sher Ali Khan', 'urdu_name' => 'ناڑ شیر علی خان', 'code' => 82111030],
            ['name' => 'Rawali', 'urdu_name' => 'راولی', 'code' => 82111032],
            ['name' => 'Swanj', 'urdu_name' => 'سوانج', 'code' => 82111035],
            ['name' => 'Thub', 'urdu_name' => 'تھب', 'code' => 82111036],
            ['name' => 'Topi', 'urdu_name' => 'ٹوپی', 'code' => 82111037],
            ['name' => 'Bani Passari', 'urdu_name' => 'بنی پساری', 'code' => 82111039],
            ['name' => 'Chammyati', 'urdu_name' => 'چمیاٹی', 'code' => 82121007],
            ['name' => 'Chirala', 'urdu_name' => 'چڑالہ', 'code' => 82121011],
            ['name' => 'Mallot', 'urdu_name' => 'ملوٹ', 'code' => 82121040],
        ];



        // foreach ($union_councils as $uc) {
        //     Demography::create([
        //         'name' => $uc['name'],
        //         'urdu_name' => $uc['urdu_name'],
        //         'type' => 'UNION_COUNCIL',
        //         // 'parent_id' => Demography::where('name', $uc['city'])->first()->id,
        //         // 'is_ajk_district' => true,
        //         'code' => $uc['code']
        //     ]);
        // }

        $cityCodes = Demography::where('type', 'CITY')
            ->whereNotNull('code')
            ->pluck('id', 'code'); // [8132 => 10, 8231 => 11]

        foreach ($union_councils as $uc) {

            $parentId = null;

            if (!empty($uc['code'])) {

                
                $ucPrefix = substr((string)$uc['code'], 0, 4);

                $parentId = $cityCodes[$ucPrefix] ?? null;
            }

            Demography::create([
                'name' => $uc['name'],
                'urdu_name' => $uc['urdu_name'],
                'type' => 'UNION_COUNCIL',
                'parent_id' => $parentId,
                'code' => $uc['code']
            ]);
        }



        DB::commit();
        Schema::enableForeignKeyConstraints();
    }
}
