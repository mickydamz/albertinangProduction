<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\User;
use App\Models\Category;
use App\Models\Country;

class DistributorList extends Component
{
    use WithPagination;

    public $search = '';
    public $hasProducts = '';
    public $sortBy = 'name';  
    public $sortDirection = 'asc';  
    public $categoryFilter = '';  
    public $countryFilter = '';  // Country filter
    public $cityFilter = '';  // City filter
    
    // Predefined list of popular cities based on countries
    public $popularCities = [
       "Afghanistan" => ["Kabul", "Herat", "Mazar-i-Sharif", "Kandahar", "Jalalabad", "Khost", "Nangarhar", "Balkh", "Ghazni", "Puli Khumri", "Bamyan", "Qandahar", "Kunduz", "Farah"],
"Albania" => ["Tirana", "Durrës", "Shkodër", "Elbasan", "Vlorë", "Fier", "Kavajë", "Korçë", "Berat", "Shijak", "Përmet", "Lushnjë", "Sarandë", "Pogradec"],
"Algeria" => ["Algiers", "Oran", "Constantine", "Annaba", "Blida", "Batna", "Sétif", "Tlemcen", "Béjaïa", "Chlef", "Biskra", "Ouargla", "El Oued", "Mostaganem"],
"Andorra" => ["Andorra la Vella", "Escaldes-Engordany", "Encamp", "Sant Julià de Lòria", "La Massana", "Ordino", "Canillo", "Escaldes", "El Serrat", "Aubinyà", "Arinsal", "Soldeu", "Ransol", "Pal"],
"Angola" => ["Luanda", "Lubango", "Lobito", "Kuito", "Huambo", "Benguela", "Malanje", "Uíge", "Cabinda", "Namibe", "Saurimo", "Cuito Cuanavale", "Luena", "Luau"],
"Antigua and Barbuda" => ["St. John's", "All Saints", "Liberta", "Five Islands Village", "Falmouth", "Pineapple", "English Harbour", "Bolands", "Cassada Gardens", "Old Road", "Potters Village", "Pigotts", "Saint George", "Glebe"],
"Argentina" => ["Buenos Aires", "Córdoba", "Rosario", "Mendoza", "La Plata", "Tucumán", "Salta", "San Juan", "Mar del Plata", "San Miguel de Tucumán", "Resistencia", "Neuquén", "Posadas", "San Salvador de Jujuy"],
"Armenia" => ["Yerevan", "Gyumri", "Vanadzor", "Vagharshapat", "Vayk", "Kapan", "Artik", "Kirovakan", "Goris", "Stepanakert", "Ashtarak", "Abovyan", "Charentsavan", "Ijevan"],
"Australia" => ["Sydney", "Melbourne", "Brisbane", "Perth", "Adelaide", "Canberra", "Hobart", "Darwin", "Gold Coast", "Newcastle", "Wollongong", "Geelong", "Cairns", "Townsville"],
"Austria" => ["Vienna", "Salzburg", "Graz", "Innsbruck", "Linz", "Klagenfurt", "Villach", "Wels", "Sankt Pölten", "Leonding", "Dornbirn", "Feldkirch", "Bregenz", "Hallein"],
"Azerbaijan" => ["Baku", "Ganja", "Mingachevir", "Sumqayit", "Mingachevir", "Lankaran", "Shaki", "Guba", "Mingachevir", "Barda", "Salyan", "Yevlakh", "Ganja", "Shamkir"],
"Bahamas" => ["Nassau", "Freeport", "West End", "Cooper's Town", "George Town", "Marsh Harbour", "Exuma", "Cat Island", "Long Island", "San Salvador", "Andros Town", "Bimini", "Chub Cay", "Grand Bahama"],
"Bahrain" => ["Manama", "Riffa", "Muharraq", "Hamad Town", "Sitra", "Isa Town", "Budaiya", "Sanabis", "Juffair", "Al-Khobar", "Al-Hidd", "Zallaq", "Madinat Hamad", "A'ali"],
"Bangladesh" => ["Dhaka", "Chittagong", "Khulna", "Rajshahi", "Sylhet", "Barisal", "Rangpur", "Mymensingh", "Rajbari", "Narayanganj", "Gazipur", "Narsingdi", "Comilla", "Tangail"],
"Barbados" => ["Bridgetown", "Speightstown", "Oistins", "Holetown", "Worthing", "Saint James", "Saint Philip", "Saint John", "Saint George", "Saint Michael", "Saint Lucy", "Saint Andrew", "Saint Thomas", "Saint Joseph"],
"Belarus" => ["Minsk", "Gomel", "Mogilev", "Vitebsk", "Grodno", "Brest", "Baranovichi", "Polotsk", "Bobruisk", "Pinsk", "Slutsk", "Zhodino", "Smolensk", "Navapolatsk"],
"Belgium" => ["Brussels", "Antwerp", "Ghent", "Bruges", "Liège", "Leuven", "Namur", "Charleroi", "Mons", "Wavre", "Mechelen", "Kortrijk", "Oostende", "Sint-Niklaas"],
"Belize" => ["Belmopan", "Belize City", "San Ignacio", "Corozal Town", "Orange Walk Town", "Punta Gorda", "Cayo", "Benque Viejo del Carmen", "Placencia", "Dangriga", "Belama", "Ladyville", "La Democracia", "Hattieville"],
"Benin" => ["Porto-Novo", "Cotonou", "Djougou", "Parakou", "Bohicon", "Abomey", "Kandi", "Ouidah", "Natitingou", "Tanguieta", "Savalou", "Allada", "Tchaourou", "Lokossa"],
"Bhutan" => ["Thimphu", "Paro", "Punakha", "Phuentsholing", "Wangdue Phodrang", "Haa", "Jakar", "Trongsa", "Samdrup Jongkhar", "Zhemgang", "Gelegphu", "Lhuntse", "Mongar", "Trashigang"],
"Bolivia" => ["La Paz", "Santa Cruz de la Sierra", "Cochabamba", "Sucre", "Oruro", "Potosí", "Tarija", "El Alto", "Trinidad", "Yacuiba", "Riberalta", "Cobija", "Beni", "Villazón"],
"Bosnia and Herzegovina" => ["Sarajevo", "Banja Luka", "Zenica", "Tuzla", "Mostar", "Bijeljina", "Tuzla", "Zenica", "Travnik", "Bugojno", "Prijedor", "Doboj", "Brčko", "Livno"],
"Botswana" => ["Gaborone", "Francistown", "Molepolole", "Maun", "Selibe-Phikwe", "Lobatse", "Palapye", "Jwaneng", "Orapa", "Gantsi", "Serowe", "Tshabong", "Kasane", "Kanye"],
"Brazil" => ["São Paulo", "Rio de Janeiro", "Salvador", "Brasília", "Fortaleza", "Belo Horizonte", "Manaus", "Curitiba", "Recife", "Porto Alegre", "Goiânia", "Belém", "Campinas", "São Luís"],
"Brunei" => ["Bandar Seri Begawan", "Kuala Belait", "Seria", "Tutong", "Temburong", "Muara", "Labu", "Kilanas", "Tungku", "Nassau", "Serasa", "Pusat Bandar", "Sungai Akar", "Mambong"],
"Bulgaria" => ["Sofia", "Plovdiv", "Varna", "Burgas", "Ruse", "Stara Zagora", "Pleven", "Sliven", "Dobrich", "Shumen", "Pernik", "Blagoevgrad", "Kyustendil", "Montana"],
"Burkina Faso" => ["Ouagadougou", "Bobo-Dioulasso", "Koudougou", "Banfora", "Ouahigouya", "Kongoussi", "Tenkodogo", "Zorgho", "Dori", "Fada N'gourma", "Gaoua", "Manga", "Houndé", "Orodara"],
"Burundi" => ["Bujumbura", "Gitega", "Ngozi", "Ruyigi", "Muyinga", "Makamba", "Kayanza", "Cankuzo", "Muramvya", "Kirundo", "Rutana", "Bubanza", "Muramvya", "Bururi"],
            "Cabo Verde" => ["Praia", "Mindelo", "Santa Maria", "Assomada", "São Filipe", "Tarrafal", "São Domingos", "Ribeira Grande", "Porto Novo", "Espargos", "Ponta do Sol", "Cova Figueira", "Bubista", "São Lourenço dos Órgãos"],
            "Cambodia" => ["Phnom Penh", "Siem Reap", "Battambang", "Sihanoukville", "Kampong Cham", "Kampong Thom", "Pursat", "Takeo", "Banteay Meanchey", "Svay Rieng", "Kep", "Prey Veng", "Kandal", "Ratanakiri"],
            "Cameroon" => ["Yaoundé", "Douala", "Garoua", "Bamenda", "Bafoussam", "Maroua", "Bertoua", "Ngaoundéré", "Dschang", "Ebolowa", "Nkongsamba", "Foumban", "Limbe", "Buea"],
            "Canada" => ["Toronto", "Vancouver", "Montreal", "Calgary", "Ottawa", "Edmonton", "Winnipeg", "Quebec City", "Hamilton", "Kitchener", "London", "Halifax", "Victoria", "Mississauga"],
            "Central African Republic" => ["Bangui", "Bimbo", "Berbérati", "Carnot", "Bata", "Kaga-Bandoro", "Sibut", "Bria", "Mbaïki", "Nana-Grébizi", "Bossangoa", "Bouar", "Nola", "Bozoum"],
            "Chad" => ["N'Djamena", "Moundou", "Sarh", "Abeché", "Kélo", "Moussoro", "Faya-Largeau", "Am Timan", "Chad", "Béline", "Koumra", "Borkou", "Koumra", "Moissala"],
            "Chile" => ["Santiago", "Valparaíso", "Concepción", "Antofagasta", "La Serena", "Temuco", "Rancagua", "Talca", "Arica", "Iquique", "Viña del Mar", "Curicó", "Osorno", "Calama"],
            "China" => ["Beijing", "Shanghai", "Guangzhou", "Shenzhen", "Chengdu", "Xi'an", "Hangzhou", "Nanjing", "Wuhan", "Tianjin", "Dongguan", "Chongqing", "Shenyang", "Harbin"],
            "Colombia" => ["Bogotá", "Medellín", "Cali", "Barranquilla", "Cartagena", "Cúcuta", "Bucaramanga", "Pereira", "Santa Marta", "Manizales", "Villavicencio", "Neiva", "Popayán", "Tunja"],
            "Comoros" => ["Moroni", "Moutsamoudou", "Fomboni", "Dzaoudzi", "Domoni", "Bumbweni", "Voinjama", "Itsandra", "Oichili", "Ngazidja", "Chiconi", "Mouhani", "Chandama", "Mbweni"],
            "Congo (Congo-Brazzaville)" => ["Brazzaville", "Pointe-Noire", "Dolisie", "Oyo", "Nkayi", "Kouilou", "Kinkala", "Ouesso", "Pouss", "Hinda", "Madingou", "Loubomo", "Mossendjo", "Likouala"],
            "Congo (Democratic Republic of the Congo)" => ["Kinshasa", "Lubumbashi", "Mbuji-Mayi", "Kisangani", "Kananga", "Goma", "Bukavu", "Kikwit", "Kolwezi", "Matadi", "Tshikapa", "Isiro", "Bunia", "Bandundu"],
            "Costa Rica" => ["San José", "Alajuela", "Cartago", "Heredia", "Liberia", "Puntarenas", "San Isidro", "Quesada", "Guápiles", "Tilarán", "Cañas", "Nicoya", "Santa Cruz", "Esparza"],
            "Croatia" => ["Zagreb", "Split", "Rijeka", "Dubrovnik", "Osijek", "Zadar", "Pula", "Šibenik", "Varazdin", "Slavonski Brod", "Karlovac", "Varaždin", "Sisak", "Bjelovar"],
            "Cuba" => ["Havana", "Santiago de Cuba", "Camagüey", "Holguín", "Guantánamo", "Santa Clara", "Cienfuegos", "Pinar del Río", "Matanzas", "Las Tunas", "Bayamo", "Ciego de Ávila", "Sancti Spíritus", "Trinidad"],
            "Cyprus" => ["Nicosia", "Limassol", "Larnaca", "Famagusta", "Paphos", "Kyrenia", "Protaras", "Paralimni", "Ayia Napa", "Polis", "Kato Paphos", "Pomos", "Deryneia", "Morphou"],
            "Czech Republic" => ["Prague", "Brno", "Ostrava", "Plzeň", "Liberec", "Olomouc", "Hradec Králové", "Pardubice", "Ústí nad Labem", "Zlín", "Karlovy Vary", "Jihlava", "Teplice", "Mladá Boleslav"],
            "Denmark" => ["Copenhagen", "Aarhus", "Odense", "Aalborg", "Esbjerg", "Randers", "Kolding", "Vejle", "Horsens", "Herning", "Silkeborg", "Frederiksberg", "Glamsbjerg", "Koge"],
            "Djibouti" => ["Djibouti City", "Ali Sabieh", "Tadjourah", "Obock", "Dikhil", "Arta", "Balho", "Tadjourah", "Moucha", "Ali Sabieh", "Khor Angar", "Dadda", "Ali Sabeh", "Randa"],
            "Dominica" => ["Roseau", "Portsmouth", "Marigot", "Pointe Michel", "Grand Bay", "La Plaine", "Mahaut", "Belfast", "St. Joseph", "Castle Bruce", "Good Hope", "Salisbury", "Colihaut", "Bagatelle"],
            "Dominican Republic" => ["Santo Domingo", "Santiago de los Caballeros", "Puerto Plata", "La Romana", "San Cristóbal", "San Pedro de Macorís", "Higuey", "Moca", "San Francisco de Macorís", "Bonao", "Punta Cana", "Jarabacoa", "Constanza", "Nagua"],
            "Ecuador" => ["Quito", "Guayaquil", "Cuenca", "Santo Domingo", "Ambato", "Machala", "Loja", "Durán", "Portoviejo", "Riobamba", "Esmeraldas", "Ibarra", "Latacunga", "Quevedo"],
            "Egypt" => ["Cairo", "Alexandria", "Giza", "Sharm El Sheikh", "Luxor", "Port Said", "Suez", "Aswan", "Mansoura", "Tanta", "Ismailia", "Fayoum", "Hurghada", "Damanhur"],
            "El Salvador" => ["San Salvador", "Santa Ana", "San Miguel", "Sonsonate", "Ahuachapán", "La Libertad", "Zacatecoluca", "Usulután", "San Vicente", "Cojutepeque", "La Unión", "Ilopango", "Soyapango", "Chalatenango"],
            "Equatorial Guinea" => ["Malabo", "Bata", "Ebebiyin", "Aconibe", "Luba", "Mongomo", "Evinayong", "Ncue", "Mbini", "Acurenam", "Micomeseng", "Asem", "Nsok", "Bata"],
            "Eritrea" => ["Asmara", "Mendefera", "Massawa", "Keren", "Asmara", "Dekemhare", "Adi Quala", "Barentu", "Senafe", "Kerkebet", "Mendefera", "Nakfa", "Tesseney", "Agordat"],
            "Estonia" => ["Tallinn", "Tartu", "Narva", "Pärnu", "Kohtla-Järve", "Rakvere", "Viljandi", "Jõhvi", "Valga", "Paide", "Kuressaare", "Võru", "Hiiu", "Jõgeva"],
            "Eswatini" => ["Mbabane", "Manzini", "Lobamba", "Nhlangano", "Big Bend", "Piggs Peak", "Simunye", "Mhlume", "Sidvokodvo", "Lomahasha", "Luhleko", "Tikhuba", "Nkambeni", "Kwaluseni"],
            "Ethiopia" => ["Addis Ababa", "Dire Dawa", "Mekelle", "Bahir Dar", "Gondar", "Awasa", "Jimma", "Hawassa", "Jijiga", "Debre Birhan", "Debre Markos", "Harar", "Weldiya", "Shashamene"],
            "Fiji" => ["Suva", "Nadi",
            
             "Lautoka", "Labasa", "Ba", "Levuka", "Sigatoka", "Nasinu", "Korolevu", "Nausori", "Tavua", "Savusavu", "Lami", "Rakiraki"],
            "Finland" => ["Helsinki", "Espoo", "Tampere", "Vantaa", "Oulu", "Turku", "Jyväskylä", "Lahti", "Kuopio", "Pori", "Lappeenranta", "Kotka", "Rovaniemi", "Seinäjoki"],
            "France" => ["Paris", "Marseille", "Lyon", "Toulouse", "Nice", "Nantes", "Strasbourg", "Montpellier", "Bordeaux", "Lille", "Rennes", "Le Havre", "Saint-Étienne", "Toulon"],
            
            
      "Gabon" => [
    "Libreville", "Port-Gentil", "Franceville", "Koulamoutou", "Tchibanga", 
    "Moanda", "Ovan", "Mouila", "Bitam", "Lambaréné", 
    "Lastoursville", "Ewone", "Sao", "Samba", "Ogooué-Ivindo"
],

"Gambia" => [
    "Banjul", "Serrekunda", "Brikama", "Bakau", "Basse Santa Su", 
    "Soma", "Latri Kunda", "Busumbala", "Fajikunda", "Jambanjelly", 
    "Kerr Serign", "Banjulinding", "Kafuta", "Bansang", "Foni"
],

"Georgia" => [
    "Tbilisi", "Batumi", "Kutaisi", "Zugdidi", "Rustavi", 
    "Zestaponi", "Samtredia", "Gori", "Senaki", "Telavi", 
    "Khashuri", "Batumi", "Mtskheta", "Vani", "Ozurgeti"
],

"Germany" => [
    "Berlin", "Munich", "Hamburg", "Frankfurt", "Cologne", 
    "Stuttgart", "Düsseldorf", "Leipzig", "Dresden", "Bremen", 
    "Hannover", "Nuremberg", "Essen", "Duisburg", "Bochum"
],

"Ghana" => [
    "Accra", "Kumasi", "Takoradi", "Tamale", "Ashaiman", 
    "Cape Coast", "Koforidua", "Sekondi-Takoradi", "Wa", "Sunyani", 
    "Bolgatanga", "Ho", "Suhum", "Nkawkaw", "Techiman"
],

"Greece" => [
    "Athens", "Thessaloniki", "Patras", "Heraklion", "Larissa", 
    "Volos", "Rhodes", "Ioannina", "Chania", "Kavala", 
    "Serres", "Corfu", "Kalamata", "Katerini", "Trikala"
],

"Grenada" => [
    "St. George's", "Gouyave", "Grenville", "Victoria", "La Sagesse", 
    "St. David's", "St. John's", "Beausejour", "Reverand", "Grand Anse"
],

"Guatemala" => [
    "Guatemala City", "Antigua Guatemala", "Quetzaltenango", "Escuintla", "Chimaltenango", 
    "Mazatenango", "Puerto Barrios", "Cobán", "Retalhuleu", "San Marcos", 
    "Jalapa", "Jutiapa", "Guastatoya", "Sololá", "Flores"
],

"Guinea" => [
    "Conakry", "Nzérékoré", "Kankan", "Kindia", "Faranah", 
    "Labé", "Mamou", "Boke", "Coyah", "Kérouané", 
    "Dabola", "Fria", "Siguiri", "Macenta", "Kissidougou"
],

"Guinea-Bissau" => [
    "Bissau", "Bafatá", "Mansôa", "Bolama", "Quebo", 
    "Cacheu", "Gabu", "Biombo", "Farim", "Enxude", 
    "Varela", "Bissau", "Piche", "Cantanhê", "Prabis"
],

"Guyana" => [
    "Georgetown", "Linden", "New Amsterdam", "Bartica", "Rosignol", 
    "Parika", "Skeldon", "Port Mourant", "Anna Regina", "Pomeroon", 
    "Lethem", "Aurora", "Kumaka", "Mabaruma", "Linden"
],

"Haiti" => [
    "Port-au-Prince", "Cap-Haïtien", "Delmas", "Carrefour", "Petionville", 
    "Jacmel", "Leogane", "Saint-Marc", "Les Cayes", "Gonaives", 
    "Port-de-Paix", "Hinche", "Lascahobas", "Miragoane", "Coteaux"
],

"Honduras" => [
    "Tegucigalpa", "San Pedro Sula", "La Ceiba", "Choloma", "El Progreso", 
    "Comayagua", "Siguatepeque", "Tocoa", "Danli", "Santa Rosa de Copan", 
    "Juticalpa", "La Lima", "Choluteca", "Ocotepeque", "Gracias"
],

"Hungary" => [
    "Budapest", "Debrecen", "Szeged", "Miskolc", "Pécs", 
    "Győr", "Nyíregyháza", "Kecskemét", "Székesfehérvár", "Sopron", 
    "Eger", "Veszprém", "Pápa", "Zalaegerszeg", "Tatabánya"
],

"Iceland" => [
    "Reykjavík", "Akureyri", "Reykjanesbær", "Kopavogur", "Hafnarfjordur", 
    "Reykholt", "Egilsstaðir", "Húsavík", "Selfoss", "Ísafjörður", 
    "Blönduós", "Borgarnes", "Akranes", "Vestmannaeyjar", "Keflavik"
],

"India" => [
    "New Delhi", "Mumbai", "Bangalore", "Kolkata", "Chennai", 
    "Hyderabad", "Ahmedabad", "Pune", "Surat", "Jaipur", 
    "Lucknow", "Kanpur", "Nagpur", "Indore", "Vadodara"
],

"Indonesia" => [
    "Jakarta", "Surabaya", "Bandung", "Medan", "Yogyakarta", 
    "Semarang", "Makassar", "Palembang", "Tangerang", "Malang", 
    "Samarinda", "Bali", "Manado", "Ambon", "Batam"
],

"Iran" => [
    "Tehran", "Isfahan", "Mashhad", "Shiraz", "Tabriz", 
    "Kermanshah", "Ahvaz", "Qom", "Rasht", "Yazd", 
    "Kerman", "Urmia", "Zahedan", "Ardabil", "Sanandaj"
],

"Iraq" => [
    "Baghdad", "Basra", "Erbil", "Mosul", "Sulaymaniyah", 
    "Najaf", "Kirkuk", "Karbala", "Ramadi", "Duhok", 
    "Amarah", "Diwaniya", "Samawa", "Hilla", "Maysan"
],

"Ireland" => [
    "Dublin", "Cork", "Limerick", "Galway", "Waterford", 
    "Belfast", "Derry", "Sligo", "Kilkenny", "Tralee", 
    "Cavan", "Wexford", "Clonmel", "Longford", "Ennis"
],

"Israel" => [
    "Jerusalem", "Tel Aviv", "Haifa", "Beersheba", "Nazareth", 
    "Ashdod", "Eilat", "Rishon LeZion", "Netanya", "Bat Yam", 
    "Holon", "Petah Tikva", "Bnei Brak", "Herzliya", "Kfar Saba"
],

"Italy" => [
    "Rome", "Milan", "Naples", "Turin", "Palermo", 
    "Genoa", "Bologna", "Florence", "Bari", "Catania", 
    "Venice", "Verona", "Messina", "Padua", "Trieste"
],

"Jamaica" => [
    "Kingston", "Montego Bay", "Mandeville", "Ocho Rios", "Negril", 
    "Portmore", "Spanish Town", "Mandeville", "May Pen", "Brown's Town", 
    "Linstead", "Santa Cruz", "Black River", "Old Harbour", "Falmouth"
],

"Japan" => [
    "Tokyo", "Osaka", "Kyoto", "Hokkaido", "Fukuoka", 
    "Kobe", "Sapporo", "Nagoya", "Yokohama", "Hiroshima", 
    "Sendai", "Fukuoka", "Kawasaki", "Saitama", "Chiba"
],

"Jordan" => [
    "Amman", "Zarqa", "Irbid", "Aqaba", "Mafraq", 
    "Salt", "Madaba", "Karak", "Tafilah", "Ma'an", 
    "Ajloun", "Russeifa", "Jordan Valley", "Jiza", "Al-Balqa"
],

"Kazakhstan" => [
    "Almaty", "Nur-Sultan", "Shymkent", "Karaganda", "Aktobe", 
    "Taraz", "Pavlodar", "Ust-Kamenogorsk", "Kostanay", "Atyrau", 
    "Semey", "Zhangiz-Tobe", "Kyzylorda", "Uralsk", "Petropavlovsk"
],

"Kenya" => [
    "Nairobi", "Mombasa", "Kisumu", "Nakuru", "Eldoret", 
    "Thika", "Nyeri", "Kisii", "Meru", "Machakos", 
    "Kitui", "Voi", "Narok", "Garissa", "Wajir"
],

"Kiribati" => [
    "South Tarawa", "Betio", "Bairiki", "Buota", "Tabuaeran", 
    "Marakei", "Abaiang", "Banaba", "Nonouti", "Tabiteuea", 
    "Butaritari", "Aranuka", "Nikunau", "Onotoa", "Tamana"
],

"Korea, North" => [
    "Pyongyang", "Namp'o", "Wonsan", "Sinuiju", "Chongjin", 
    "Kaesong", "Hamhung", "Haeju", "Sariwon", "Pyongsong", 
    "Chongju", "Kimchaek", "Munchon", "Samjiyon", "Kaechon"
],

"Korea, South" => [
    "Seoul", "Busan", "Incheon", "Daegu", "Daejeon", 
    "Gwangju", "Ulsan", "Suwon", "Changwon", "Jeonju", 
    "Cheongju", "Jeju", "Gimhae", "Pohang", "Anyang"
],

"Kuwait" => [
    "Kuwait City", "Hawalli", "Mishref", "Salmiya", "Fahaheel", 
    "Jahra", "Farwaniya", "Ahmadi", "Sabah Al Salem", "Kheiran", 
    "Rumaithiya", "Shuwaikh", "Mangaf", "Kuwait Bay", "Abdali"
],

"Kyrgyzstan" => [
    "Bishkek", "Osh", "Jalal-Abad", "Karakol", "Tokmok", 
    "Kemin", "Naryn", "Batken", "Uzgen", "Talas", 
    "Karakol", "Tokmok", "Chuy", "Suusamyr", "At-Bashi"
],

"Laos" => [
    "Vientiane", "Luang Prabang", "Pakse", "Savannakhet", "Xieng Khouang", 
    "Thakhek", "Champasak", "Mouang Xay", "Attapeu", "Bokeo", 
    "Huaphanh", "Phongsali", "Saravane", "Vang Vien", "Sepon"
],


"Nauru" => [
    "Yaren", "Anibare", "Baiti", "Meneng", "Ewa", 
    "Aiwo", "Boe", "Denigomodu", "Nijon", "Uaboe"
],

"Nepal" => [
    "Kathmandu", "Pokhara", "Lalitpur", "Bhaktapur", "Pokhara", 
    "Biratnagar", "Janakpur", "Butwal", "Dhangadhi", "Bhairahawa", 
    "Hetauda", "Birganj", "Dhulikhel", "Patan", "Itahari"
],

"Netherlands" => [
    "Amsterdam", "Rotterdam", "The Hague", "Utrecht", "Eindhoven", 
    "Groningen", "Tilburg", "Almere", "Breda", "Nijmegen", 
    "Enschede", "Haarlem", "Arnhem", "Leiden", "Maastricht"
],

"New Zealand" => [
    "Auckland", "Wellington", "Christchurch", "Hamilton", "Dunedin", 
    "Tauranga", "Napier", "Hastings", "Palmerston North", "Invercargill", 
    "Whangarei", "Rotorua", "New Plymouth", "Queenstown", "Taupo"
],

"Nicaragua" => [
    "Managua", "León", "Granada", "Masaya", "Chinandega", 
    "Estelí", "Rivas", "Bluefields", "Jinotega", "Matagalpa", 
    "Sébaco", "Somoto", "Diriamba", "San Juan del Sur", "Boaco"
],

"Niger" => [
    "Niamey", "Maradi", "Zinder", "Tahoua", "Agadez", 
    "Tillabéri", "Diffa", "Dosso", "Tessaoua", "Gaya", 
    "Birni N'Konni", "Niamey", "Aderbissinat", "Madarounfa", "Tibiri"
],

"Nigeria" => [
    "Lagos", "Abuja", "Port Harcourt", "Ibadan", "Kaduna", 
    "Kano", "Benin City", "Maiduguri", "Aba", "Jos", 
    "Zaria", "Ilorin", "Enugu", "Warri", "Sokoto"
],

"North Macedonia" => [
    "Skopje", "Bitola", "Prilep", "Ohrid", "Veles", 
    "Kumanovo", "Strumica", "Struga", "Sveti Nikole", "Kavadarci", 
    "Kichevo", "Debar", "Negotino", "Delčevo", "Berovo"
],

"Norway" => [
    "Oslo", "Bergen", "Stavanger", "Trondheim", "Drammen", 
    "Dundas", "Kristiansand", "Fredrikstad", "Tromsø", "Sandnes", 
    "Bodø", "Arendal", "Porsgrunn", "Skien", "Larvik"
],

"Oman" => [
    "Muscat", "Sohar", "Salalah", "Nizwa", "Sur", 
    "Ibri", "Rustaq", "Buraimi", "Khasab", "Ibra", 
    "Bahla", "Dhofar", "Al Kamil Wal Wafi", "Al Buraimi", "Nizwah"
],

"Pakistan" => [
    "Karachi", "Lahore", "Islamabad", "Rawalpindi", "Peshawar", 
    "Quetta", "Faisalabad", "Multan", "Sialkot", "Gujranwala", 
    "Azad Kashmir", "Swat", "Bahawalpur", "Ghotki", "Hyderabad"
],

"Palau" => [
    "Ngerulmud", "Koror", "Melekeok", "Airai", "Aimeliik", 
    "Ngaraard", "Ngchesar", "Peleliu", "Sonsorol", "Hatohobei"
],

"Panama" => [
    "Panama City", "Colón", "David", "La Chorrera", "Santiago", 
    "Chiriquí", "Penonomé", "Bocas del Toro", "Penonomé", "Las Tablas", 
    "Chitré", "Aguadulce", "La Villa de los Santos", "Portobelo", "La Palma"
],

"Papua New Guinea" => [
    "Port Moresby", "Lae", "Mount Hagen", "Madang", "Goroka", 
    "Kokopo", "Rabaul", "Buka", "Kimbe", "Popondetta", 
    "Wewak", "Madang", "Alotau", "Honiara", "Arawa"
],

"Paraguay" => [
    "Asunción", "Ciudad del Este", "Encarnación", "San Lorenzo", "Lambare", 
    "Luque", "Fernando de la Mora", "Capiatá", "Minga Guazú", "Pedro Juan Caballero", 
    "Villa Elisa", "Caaguazú", "Villarrica", "Coronel Oviedo", "Concepción"
],

"Peru" => [
    "Lima", "Arequipa", "Cusco", "Trujillo", "Chiclayo", 
    "Piura", "Iquitos", "Chimbote", "Puno", "Tacna", 
    "Chanchamayo", "Huancayo", "Ayacucho", "Moquegua", "Huaraz"
],

"Philippines" => [
    "Manila", "Quezon City", "Cebu City", "Davao City", "Zamboanga City", 
    "Taguig", "Makati", "Pasig", "Cagayan de Oro", "Iloilo City", 
    "Dumaguete", "Baguio", "Tarlac", "Marikina", "General Santos"
],

"Poland" => [
    "Warsaw", "Kraków", "Łódź", "Wrocław", "Poznań", 
    "Gdańsk", "Szczecin", "Bydgoszcz", "Lublin", "Katowice", 
    "Białystok", "Gdynia", "Częstochowa", "Radom", "Torun"
],

"Portugal" => [
    "Lisbon", "Porto", "Braga", "Coimbra", "Funchal", 
    "Amadora", "Vila Nova de Gaia", "Aveiro", "Setúbal", "Sintra", 
    "Évora", "Leiria", "Faro", "Viseu", "Bragança"
],

"Qatar" => [
    "Doha", "Al Rayyan", "Al Khor", "Al Wakrah", "Umm Salal", 
    "Al Daayen", "Al Shahaniya", "Mesaieed", "Ras Laffan", "Al Khobar"
],

"Romania" => [
    "Bucharest", "Cluj-Napoca", "Timișoara", "Iași", "Constanța", 
    "Craiova", "Galați", "Brașov", "Pitești", "Oradea", 
    "Sibiu", "Ploiești", "Bacău", "Arad", "Mureș"
],

"Russia" => [
    "Moscow", "Saint Petersburg", "Novosibirsk", "Yekaterinburg", "Nizhny Novgorod", 
    "Kazan", "Chelyabinsk", "Omsk", "Samara", "Rostov-on-Don", 
    "Ufa", "Volgograd", "Perm", "Voronezh", "Saratov"
],

"Rwanda" => [
    "Kigali", "Butare", "Gitarama", "Musanze", "Rubavu", 
    "Huye", "Rwamagana", "Kayonza", "Nyamata", "Gisenyi", 
    "Kibuye", "Cyangugu", "Nyanza", "Muhanga", "Kirehe"
],

"Saint Kitts and Nevis" => [
    "Basseterre", "Charlestown", "Sandy Point Town", "Cotton Ground", "New Town", 
    "Tabernacle", "St. Peter's", "Old Road", "Cayon", "Verchild's"
],

"Saint Lucia" => [
    "Castries", "Gros Islet", "Vieux Fort", "Soufrière", "Dennery", 
    "Bisee", "Micoud", "Laborie", "Canaries", "Anse La Raye", 
    "Dennery", "Belfond", "Praslin", "Desruisseaux", "Chesneau"
],

"Saint Vincent and the Grenadines" => [
    "Kingstown", "Georgetown", "Barrouallie", "Bequia", "Canouan", 
    "Union Island", "Mustique", "Tobago Cays", "Port Elizabeth", "Layou"
]
,
        "Samoa" => ["Apia", "Faleula", "Vaitele", "Leulumoega", "Siumu"],
     "San Marino" => [
    "City of San Marino", "Serravalle", "Borgo Maggiore", "Faetano", "Chiesanuova",
    "Montegiardino", "Acquaviva", "Domagnano", "Fiorentino", "San Leo"
],

"Sao Tome and Principe" => [
    "São Tomé", "Principe", "Neves", "Trindade", "Bombe Agua",
    "Santa Catarina", "Vila Real", "São João dos Angolares", "Ribeira Afonso", "Angolares"
],

"Saudi Arabia" => [
    "Riyadh", "Jeddah", "Mecca", "Medina", "Khobar",
    "Dammam", "Makkah", "Taif", "Abha", "Al Khobar",
    "Najran", "Tabuk", "Hail", "Jubail", "Buraidah"
],

"Senegal" => [
    "Dakar", "Touba", "Saint-Louis", "Ziguinchor", "Kaolack",
    "Thiès", "Mbour", "Fatick", "Tivaouane", "Kolda",
    "Rufisque", "Diourbel", "Bakel", "Kedougou", "Matam"
],

"Serbia" => [
    "Belgrade", "Novi Sad", "Niš", "Kragujevac", "Kraljevo",
    "Subotica", "Zrenjanin", "Novi Pazar", "Senta", "Vranje",
    "Kruševac", "Pančevo", "Leskovac", "Valjevo", "Senti"
],

"Seychelles" => [
    "Victoria", "Anse Royale", "Beau Vallon", "Praslin", "La Digue",
    "Grand Anse", "Port Glaud", "Baie Sainte Anne", "Takamaka", "Les Mamelles"
],

"Sierra Leone" => [
    "Freetown", "Bo", "Kenema", "Makeni", "Koidu",
    "Tonkolili", "Moyamba", "Bonthe", "Port Loko", "Pujehun",
    "Kailahun", "Kambia", "Falaba", "Koinadugu", "Bonthe"
],

"Singapore" => [
    "Singapore"
],

"Slovakia" => [
    "Bratislava", "Košice", "Prešov", "Nitra", "Žilina",
    "Námestovo", "Trnava", "Trenčín", "Poprad", "Martin",
    "Zvolen", "Komárno", "Prievidza", "Považská Bystrica", "Vranov nad Topľou"
],

"Slovenia" => [
    "Ljubljana", "Maribor", "Celje", "Kranj", "Koper",
    "Novo Mesto", "Murska Sobota", "Ptuj", "Velenje", "Jesenice",
    "Sežana", "Trbovlje", "Izola", "Domžale", "Radovljica"
],

"Solomon Islands" => [
    "Honiara", "Gizo", "Auki", "Tulagi", "Lata",
    "Kirakira", "Munda", "Noro", "Buala", "Choiseul Bay",
    "Malaita", "Western Province", "Santa Isabel", "Makira", "Temotu"
],

"Somalia" => [
    "Mogadishu", "Hargeisa", "Kismayo", "Baidoa", "Bossaso",
    "Galkayo", "Burao", "Berbera", "Jowhar", "Merca",
    "Kismayo", "Qardho", "Garowe", "Hudur", "Afgooye"
]
,
       "South Africa" => [
    "Cape Town", "Johannesburg", "Durban", "Pretoria", "Port Elizabeth", 
    "Bloemfontein", "Polokwane", "Port Shepstone", "East London", "Kimberley", 
    "Nelspruit", "Pietermaritzburg", "Upington", "George", "Mthatha"
],

"South Sudan" => [
    "Juba", "Malakal", "Wau", "Aweil", "Yei", 
    "Bor", "Rumbek", "Torit", "Nimule", "Aweil"
],

"Spain" => [
    "Madrid", "Barcelona", "Valencia", "Seville", "Bilbao", 
    "Zaragoza", "Malaga", "Murcia", "Palma de Mallorca", "Las Palmas", 
    "Castellón de la Plana", "Alicante", "Cordoba", "Valladolid", "Gijón"
],

"Sri Lanka" => [
    "Colombo", "Kandy", "Galle", "Jaffna", "Negombo", 
    "Anuradhapura", "Batticaloa", "Matara", "Badulla", "Trincomalee", 
    "Kurunegala", "Kalutara", "Vavuniya", "Nuwara Eliya", "Ratnapura"
],

"Sudan" => [
    "Khartoum", "Omdurman", "Port Sudan", "Nyala", "Kassala", 
    "El Obeid", "Wadi Halfa", "Al Qadarif", "Kosti", "Atbara", 
    "Sennar", "Al-Fasher", "Juba", "El Fasher", "Medani"
],

"Suriname" => [
    "Paramaribo", "Nieuw Nickerie", "Moengo", "Albina", "St. Laurent du Maroni", 
    "Totness", "Marienburg", "Brokopondo", "Lelydorp", "Wanica"
],

"Sweden" => [
    "Stockholm", "Gothenburg", "Malmö", "Uppsala", "Västerås", 
    "Örebro", "Linköping", "Helsingborg", "Jönköping", "Norrköping", 
    "Lund", "Växjö", "Sundsvall", "Eskilstuna", "Borås"
],

"Switzerland" => [
    "Zurich", "Geneva", "Bern", "Basel", "Lausanne", 
    "Lucerne", "St. Gallen", "Lucerne", "Biel/Bienne", "Chur", 
    "Winterthur", "Lugano", "Thun", "Neuchâtel", "Fribourg"
],

"Syria" => [
    "Damascus", "Aleppo", "Homs", "Latakia", "Tartus", 
    "Hama", "Deir ez-Zor", "Raqqa", "Idlib", "Daraa", 
    "Qamishli", "Banias", "Zabadani", "Douma", "Salmiyah"
],

"Taiwan" => [
    "Taipei", "Kaohsiung", "Taichung", "Tainan", "Keelung", 
    "Taoyuan", "Hsinchu", "Chiayi", "Miaoli", "Changhua", 
    "Hualien", "Pingtung", "Yilan", "Lienchiang", "Kinmen"
],

"Tajikistan" => [
    "Dushanbe", "Khujand", "Kulob", "Bokhtar", "Istaravshan", 
    "Isfara", "Tursunzoda", "Khorugh", "Rushan", "Garm", 
    "Vahdat", "Norak", "Panjakent", "Jirgatol", "Kulyab"
],

"Tanzania" => [
    "Dar es Salaam", "Dodoma", "Mwanza", "Arusha", "Mbeya", 
    "Morogoro", "Tanga", "Zanzibar City", "Mtwara", "Shinyanga", 
    "Tabora", "Songea", "Bukoba", "Iringa", "Kigoma"
]
,
   "Thailand" => [
    "Bangkok", "Chiang Mai", "Phuket", "Pattaya", "Hua Hin", 
    "Chiang Rai", "Ayutthaya", "Nakhon Ratchasima", "Khon Kaen", "Chonburi", 
    "Udon Thani", "Nakhon Si Thammarat", "Surat Thani", "Hat Yai", "Nakhon Pathom"
],

"Togo" => [
    "Lomé", "Sokodé", "Kara", "Atakpamé", "Kpalimé", 
    "Tsévié", "Dapaong", "Bassar", "Tchamba", "Notse", 
    "Kévé", "Aneho", "Notsé", "Vogan", "Sokodé"
],

"Tonga" => [
    "Nuku'alofa", "Neiafu", "Vava'u", "Ha'apai", "Tongatapu", 
    "Mafia", "Eua", "Ata", "Niuatoputapu", "Niuafo'ou"
],

"Trinidad and Tobago" => [
    "Port of Spain", "San Fernando", "Scarborough", "Chaguanas", "Arima", 
    "Point Fortin", "Princes Town", "Rio Claro", "San Juan", "Tunapuna", 
    "St. Augustine", "Newtown", "Woodbrook", "Debe", "Marabella"
],

"Tunisia" => [
    "Tunis", "Sfax", "Sousse", "Kairouan", "Gabès", 
    "Monastir", "Bizerte", "Kasserine", "Tataouine", "Nabeul", 
    "Medenine", "Mahdia", "La Marsa", "Sidi Bouzid", "Ariana"
],

"Turkey" => [
    "Istanbul", "Ankara", "Izmir", "Bursa", "Antalya", 
    "Adana", "Konya", "Gaziantep", "Mersin", "Kayseri", 
    "Eskisehir", "Samsun", "Diyarbakir", "Trabzon", "Antakya"
]
,
     "Turkmenistan" => [
    "Ashgabat", "Mary", "Turkmenabad", "Dashoguz", "Balkanabat",
    "Bayramaly", "Serdar", "Köneürgenç", "Lebap", "Turkmenabat"
],

"Tuvalu" => [
    "Funafuti", "Vaiaku", "Nukufetau", "Nanumea", "Niulakita",
    "Niutao", "Vaitupu", "Fongafale", "Tefala", "Alofi"
],

"Uganda" => [
    "Kampala", "Gulu", "Mbarara", "Mbale", "Jinja", 
    "Wakiso", "Lira", "Fort Portal", "Masaka", "Entebbe", 
    "Arua", "Kabarole", "Kasese", "Kabale", "Hoima"
],

"Ukraine" => [
    "Kyiv", "Lviv", "Odessa", "Kharkiv", "Dnipro",
    "Zaporizhzhia", "Kherson", "Mykolaiv", "Poltava", "Chernihiv",
    "Vinnytsia", "Sumy", "Rivne", "Kryvyi Rih", "Mariupol"
],

"United Arab Emirates" => [
    "Dubai", "Abu Dhabi", "Sharjah", "Ajman", "Fujairah", 
    "Ras Al Khaimah", "Umm Al-Quwain", "Al Ain", "Khalifa City", "Jebel Ali"
],

"United Kingdom" => [
    "London", "Manchester", "Birmingham", "Liverpool", "Edinburgh",
    "Glasgow", "Leeds", "Bristol", "Sheffield", "Cardiff", 
    "Newcastle upon Tyne", "Nottingham", "Leicester", "Coventry", "Belfast"
] ,"United States" => [
    "New York City", "Los Angeles", "Chicago", "Houston", "Phoenix", 
    "Philadelphia", "San Antonio", "San Diego", "Dallas", "San Jose", 
    "Austin", "Jacksonville", "Fort Worth", "Columbus", "Indianapolis", 
    "Charlotte", "San Francisco", "Seattle", "Denver", "Washington D.C.", 
    "Boston", "El Paso", "Nashville", "Detroit", "Oklahoma City", 
    "Portland", "Las Vegas", "Memphis", "Louisville", "Baltimore", 
    "Milwaukee", "Albuquerque", "Tucson", "Fresno", "Sacramento", 
    "Kansas City", "Long Beach", "Mesa", "Atlanta", "Colorado Springs", 
    "Raleigh", "Omaha", "Miami", "Virginia Beach", "Minneapolis", 
    "Tulsa", "Bakersfield", "Wichita", "New Orleans", "Arlington",
    "Cleveland", "Tampa", "Honolulu", "Anaheim", "Aurora"
],


"Uruguay" => [
    "Montevideo", "Salto", "Paysandú", "Maldonado", "Tacuarembó", 
    "Rivera", "Durazno", "Canelones", "San José de Mayo", "Florida", 
    "Mercedes", "Lavalleja", " Rocha", "Artigas", "Treinta y Tres"
],

"Uzbekistan" => [
    "Tashkent", "Samarkand", "Bukhara", "Namangan", "Andijan", 
    "Fergana", "Khiva", "Nukus", "Termiz", "Jizzakh", 
    "Kokand", "Shahrisabz", "Zarafshan", "Bukhara", "Angren"
],

"Vanuatu" => [
    "Port Vila", "Luganville", "Norsup", "Sola", "Port Sandwich", 
    "Lamen Bay", "Isangel", "Futuna", "Tanna", "Aneityum"
],

"Vatican City" => [
    "Vatican City"
],

     "Venezuela" => [
    "Caracas", "Maracaibo", "Valencia", "Barquisimeto", "Ciudad Guayana", 
    "Maturín", "Porlamar", "San Cristóbal", "Maracay", "Barcelona", 
    "Ciudad Bolívar", "Barinas", "Cumaná", "Punto Fijo", "La Victoria"
],

"Vietnam" => [
    "Hanoi", "Ho Chi Minh City", "Da Nang", "Hai Phong", "Can Tho", 
    "Hue", "Nha Trang", "Bien Hoa", "Vinh", "Rach Gia", 
    "Phan Thiết", "Thanh Hóa", "Quy Nhon", "Nam Định", "Bac Ninh"
],

"Yemen" => [
    "Sana'a", "Aden", "Taiz", "Al Hudaydah", "Ibb", 
    "Mukalla", "Dhamar", "Al-Mukalla", "Hodeidah", "Ma'rib", 
    "Amran", "Rada'a", "Zinjibar", "Al Bayda", "Lahij"
],

"Zambia" => [
    "Lusaka", "Kitwe", "Ndola", "Kabwe", "Chingola", 
    "Livingstone", "Mufulira", "Luanshya", "Choma", 
    "Chipata", "Solwezi", "Kasama", "Mazabuka", "Monze", 
    "Sesheke", "Kantanshi", "Lukulu", "Mpika", "Mkushi"
],
"Zimbabwe" => [
    "Harare", "Bulawayo", "Mutare", "Gweru", "Kwekwe", 
    "Chitungwiza", "Masvingo", "Bindura", "Chegutu", 
    "Kadoma", "Hwange", "Victoria Falls", "Chinhoyi", 
    "Kariba", "Rusape", "Gwanda", "Zvishavane", "Chipinge", 
    "Ruwa", "Norton", "Mudzi", "Shurugwi", "Macheke", 
    "Mberengwa", "Nyanga", "Mount Darwin"
]
    ];
    

    protected $queryString = ['search', 'hasProducts', 'sortBy', 'sortDirection', 'categoryFilter', 'countryFilter', 'cityFilter'];

    public function mount()
    {
        $this->search = '';
        $this->sortBy = 'name'; 
        $this->sortDirection = 'asc'; 
        $this->categoryFilter = '';  
        $this->countryFilter = '';  // Initialize country filter
        $this->cityFilter = '';  // Initialize city filter
    }

    public function updatingSearch()
    {
        $this->resetPage(); 
    }

    public function updatingHasProducts()
    {
        $this->resetPage(); 
    }

    public function updatingCategoryFilter()
    {
        $this->resetPage(); 
    }

    public function updatingCountryFilter()
    {
        $this->resetPage();
        $this->cityFilter = '';  // Reset city filter when country changes
    }

    public function updatingCityFilter()
    {
        $this->resetPage();
    }

    // Method to update sorting properties
    public function sortBy($field)
    {
        if ($this->sortBy === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $field;
            $this->sortDirection = 'asc';
        }

        $this->resetPage(); 
    }

    public function render()
    {
        // Get all categories
        $categories = Category::all();  

        // Get all countries and order them alphabetically
        $countries = Country::orderBy('name', 'asc')->get();  

        // Get the list of cities based on the selected country filter
        $cities = [];
        if ($this->countryFilter && isset($this->popularCities[$this->countryFilter])) {
            // Sort the cities alphabetically
            $cities = collect($this->popularCities[$this->countryFilter])->sort()->toArray();
        }

        // Query to get suppliers
        $query = User::where('role', 'supplier');  

        // Apply filters
        if ($this->search) {
            $query->where('name', 'like', '%' . $this->search . '%');
        }

        if ($this->hasProducts === 'yes') {
            $query->whereHas('products');
        } elseif ($this->hasProducts === 'no') {
            $query->whereDoesntHave('products');
        }

        if ($this->categoryFilter) {
            $query->whereHas('products', function ($q) {
                $q->where('category_id', $this->categoryFilter);
            });
        }

        if ($this->countryFilter) {
            $query->where('country', $this->countryFilter);  // Assuming 'country' is a column in the User model
        }

        if ($this->cityFilter) {
            $query->where('city', $this->cityFilter);  // Assuming 'city' is a column in the User model
        }

        // Apply sorting
        $query->orderBy($this->sortBy, $this->sortDirection);  
        $distributors = $query->paginate(10);  // Get paginated results

        return view('livewire.distributor-list', [
            'distributors' => $distributors,
            'categories' => $categories,
            'countries' => $countries,  // Pass countries to view
            'cities' => $cities,  // Pass cities to view based on selected country
        ]);
    }
}
