<?php

namespace Database\Seeders;

use App\Models\Question;
use App\Models\Subject;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class QuestionSeeder extends Seeder
{
    private const DURATION_SECONDS = 60;
    private const MIN_QUESTIONS_PER_BANK = 150;

    public function run(): void
    {
        $this->moveGeographyQuestions();
        $this->removeRetiredGeneralQuestions();
        $this->removeOldSampleQuestions();
        $this->removeDuplicateQuestions();

        foreach ($this->questionBanks() as $subjectName => $questions) {
            $questions = $this->ensureMinimumQuestions($subjectName, $questions);
            $this->assertUniqueQuestionText($subjectName, $questions);

            $subject = $this->subjectFor($subjectName);
            $this->saveQuestions($subject, $questions);
        }

        $this->removeDuplicateQuestions();
        $this->deactivateEmptyGeneralSubject();
    }

    private function questionBanks(): array
    {
        return [
            'Biology' => $this->conceptQuestions('Biology', [
                ['Cell', 'the basic structural and functional unit of life'],
                ['Nucleus', 'the part of a cell that controls most cell activities'],
                ['Mitochondrion', 'the cell organelle where most energy is released'],
                ['Chlorophyll', 'the green pigment that absorbs light for photosynthesis'],
                ['Photosynthesis', 'the process by which green plants make food using light'],
                ['Respiration', 'the process by which cells release energy from food'],
                ['Tissue', 'a group of similar cells working together'],
                ['Organ', 'a body part made of tissues that performs a function'],
                ['Enzyme', 'a biological catalyst that speeds up reactions'],
                ['Diffusion', 'movement of particles from high concentration to low concentration'],
                ['Osmosis', 'movement of water molecules through a selectively permeable membrane'],
                ['Habitat', 'the natural place where an organism lives'],
                ['Ecosystem', 'a community of organisms interacting with their environment'],
                ['Producer', 'an organism that makes its own food'],
                ['Consumer', 'an organism that feeds on other organisms'],
                ['Decomposer', 'an organism that breaks down dead organic matter'],
                ['Herbivore', 'an animal that feeds mainly on plants'],
                ['Carnivore', 'an animal that feeds mainly on other animals'],
                ['Omnivore', 'an animal that feeds on both plants and animals'],
                ['Genetics', 'the study of heredity and variation'],
                ['Chromosome', 'a thread-like structure that carries genes'],
                ['DNA', 'the molecule that carries hereditary information'],
                ['Vaccine', 'a preparation that stimulates immunity against disease'],
                ['Pathogen', 'a microorganism that can cause disease'],
                ['Homeostasis', 'maintenance of a stable internal body environment'],
                ['Microscope', 'an instrument used to view very small objects'],
                ['Hygiene', 'practices that help maintain health and cleanliness'],
                ['First aid', 'immediate help given to an injured or sick person'],
                ['Recycling', 'processing used materials so they can be used again'],
                ['Transpiration', 'loss of water vapour from aerial parts of plants'],
                ['Germination', 'the process by which a seed begins to grow'],
                ['Pollination', 'transfer of pollen from anther to stigma'],
                ['Fertilization', 'fusion of male and female gametes'],
                ['Mutation', 'a sudden change in genetic material'],
            ]),
            'Chemistry' => $this->conceptQuestions('Chemistry', [
                ['Atom', 'the smallest particle of an element that keeps its properties'],
                ['Element', 'a pure substance made of only one type of atom'],
                ['Compound', 'a substance formed when elements chemically combine'],
                ['Molecule', 'two or more atoms chemically joined together'],
                ['Mixture', 'substances physically combined without chemical bonding'],
                ['Solution', 'a uniform mixture of solute and solvent'],
                ['Solvent', 'the substance that dissolves a solute'],
                ['Solute', 'the substance dissolved in a solvent'],
                ['Acid', 'a substance with pH less than 7'],
                ['Base', 'a substance with pH greater than 7'],
                ['Neutralization', 'a reaction between an acid and a base'],
                ['Catalyst', 'a substance that changes reaction speed without being used up'],
                ['Ion', 'an atom or group of atoms with an electric charge'],
                ['Covalent bond', 'a bond formed by sharing electrons'],
                ['Ionic bond', 'a bond formed by transfer of electrons'],
                ['pH scale', 'a scale used to measure acidity or alkalinity'],
                ['Oxidation', 'loss of electrons during a chemical reaction'],
                ['Reduction', 'gain of electrons during a chemical reaction'],
                ['Electrolysis', 'chemical decomposition caused by electric current'],
                ['Hydrocarbon', 'a compound containing only carbon and hydrogen'],
                ['Polymer', 'a large molecule made from repeating smaller units'],
                ['Isotope', 'atoms of the same element with different mass numbers'],
                ['Mole', 'the amount of substance containing Avogadro number of particles'],
                ['Exothermic reaction', 'a reaction that releases heat energy'],
                ['Endothermic reaction', 'a reaction that absorbs heat energy'],
                ['Electrolyte', 'a substance that conducts electricity when molten or in solution'],
                ['Alloy', 'a mixture of metals or a metal with another element'],
                ['Distillation', 'separating liquids by differences in boiling point'],
                ['Filtration', 'separating insoluble solids from liquids'],
                ['Precipitate', 'an insoluble solid formed during a reaction'],
            ]),
            'Computer Studies' => $this->conceptQuestions('Computer Studies', [
                ['Computer', 'an electronic device that accepts data and processes it'],
                ['Hardware', 'the physical parts of a computer system'],
                ['Software', 'programs and instructions used by a computer'],
                ['Keyboard', 'an input device with keys for typing'],
                ['Mouse', 'an input device used to point and select items'],
                ['Monitor', 'an output device that displays information visually'],
                ['Printer', 'an output device that produces hard copies'],
                ['Scanner', 'an input device that captures images or documents'],
                ['Calculator', 'a device used to perform mathematical operations'],
                ['CPU', 'the main processing unit of a computer'],
                ['Memory', 'the part of a computer used to store data temporarily'],
                ['Storage', 'the part of a computer used to keep data permanently'],
                ['File', 'a named collection of stored data'],
                ['Folder', 'a container used to organize files'],
                ['Internet', 'a worldwide network of connected computers'],
                ['Browser', 'software used to access web pages'],
                ['Email', 'a system for sending messages electronically'],
                ['Password', 'a secret code used to protect access'],
                ['Database', 'an organized collection of data'],
                ['Spreadsheet', 'software used to arrange data in rows and columns'],
                ['Word processor', 'software used to create and edit text documents'],
                ['Algorithm', 'a step-by-step method for solving a problem'],
                ['Debugging', 'finding and correcting errors in a program'],
                ['Cybersecurity', 'protecting computer systems and data from attack'],
                ['Backup', 'a copy of data kept for recovery'],
                ['Operating system', 'software that manages computer hardware and programs'],
                ['Network', 'a group of connected computers that share resources'],
                ['Cloud storage', 'saving data on remote servers accessed through the internet'],
                ['Malware', 'software designed to harm or exploit a computer system'],
                ['Encryption', 'converting data into a coded form for security'],
            ]),
            'Economics' => $this->conceptQuestions('Economics', [
                ['Scarcity', 'limited resources compared with unlimited wants'],
                ['Choice', 'selecting one option from available alternatives'],
                ['Opportunity cost', 'the next best alternative forgone'],
                ['Demand', 'quantity consumers are willing and able to buy'],
                ['Supply', 'quantity producers are willing and able to sell'],
                ['Market', 'any arrangement where buyers and sellers exchange goods'],
                ['Equilibrium price', 'the price where quantity demanded equals quantity supplied'],
                ['Inflation', 'a sustained rise in the general price level'],
                ['Deflation', 'a sustained fall in the general price level'],
                ['Utility', 'satisfaction gained from consuming a good or service'],
                ['Production', 'the process of creating goods and services'],
                ['Land', 'natural resources used in production'],
                ['Labour', 'human effort used in production'],
                ['Capital', 'man-made resources used for further production'],
                ['Entrepreneur', 'the organizer who combines factors of production'],
                ['Consumer', 'a person who buys goods or services for use'],
                ['Producer', 'a person or firm that makes goods or services'],
                ['Profit', 'the excess of revenue over cost'],
                ['Revenue', 'income received from selling goods or services'],
                ['Cost', 'expenses incurred in production'],
                ['Tax', 'a compulsory payment to government'],
                ['Subsidy', 'financial support given to reduce production cost'],
                ['Gross domestic product', 'total value of final goods and services produced in a country'],
                ['Exchange rate', 'the price of one currency in terms of another'],
                ['Balance of trade', 'the difference between visible exports and visible imports'],
                ['Budget', 'a financial plan of expected revenue and expenditure'],
                ['Monopoly', 'a market controlled by one seller'],
                ['Elasticity', 'responsiveness of demand or supply to price changes'],
                ['Division of labour', 'breaking production into specialized tasks'],
                ['Barter', 'exchange of goods and services without money'],
            ]),
            'English Language' => $this->englishQuestions(),
            'Geography' => $this->geographyQuestions(),
            'Government' => $this->conceptQuestions('Government', [
                ['Democracy', 'government in which people choose their leaders'],
                ['Constitution', 'a body of fundamental laws of a state'],
                ['Legislature', 'the arm of government that makes laws'],
                ['Executive', 'the arm of government that implements laws'],
                ['Judiciary', 'the arm of government that interprets laws'],
                ['Rule of law', 'the principle that everyone is subject to the law'],
                ['Separation of powers', 'division of government powers among different arms'],
                ['Federalism', 'sharing powers between central and regional governments'],
                ['Suffrage', 'the legal right to vote in elections'],
                ['Election', 'a process of choosing leaders by voting'],
                ['Political party', 'an organization seeking political power through elections'],
                ['Civil service', 'permanent officials who implement government policies'],
                ['Citizen', 'a legal member of a state'],
                ['Sovereignty', 'supreme power of a state over its affairs'],
                ['Monarchy', 'government headed by a king or queen'],
                ['Republic', 'a state with an elected or non-hereditary head'],
                ['Referendum', 'a direct vote by citizens on a public issue'],
                ['Cabinet', 'senior ministers who advise the head of government'],
                ['Bill', 'a proposed law before it is passed'],
                ['Law', 'a rule made and enforced by government'],
                ['Human rights', 'basic freedoms and protections people should enjoy'],
                ['Pressure group', 'an organization that influences government decisions'],
                ['Public opinion', 'the views held by members of the public'],
                ['Local government', 'government that manages affairs at community level'],
                ['Checks and balances', 'controls that prevent abuse of power'],
                ['Road signs', 'symbols that guide and warn road users'],
                ['Traffic light', 'a signal device that controls road movement'],
                ['Citizenship', 'membership of a country with rights and duties'],
                ['Culture', 'the way of life of a group of people'],
                ['Manifesto', 'a public statement of political plans and promises'],
                ['Quorum', 'the minimum number required for a valid meeting'],
                ['Impeachment', 'a process for removing certain public officers from office'],
                ['Delegated legislation', 'laws made by bodies authorized by parliament'],
                ['Civic responsibility', 'duties citizens perform for the good of society'],
            ]),
            'Mathematics' => $this->mathematicsQuestions(),
            'Physics' => $this->conceptQuestions('Physics', [
                ['Force', 'a push or pull that can change motion'],
                ['Newton', 'the SI unit of force'],
                ['Velocity', 'speed in a specified direction'],
                ['Acceleration', 'rate of change of velocity'],
                ['Momentum', 'the product of mass and velocity'],
                ['Work', 'force multiplied by distance moved in the force direction'],
                ['Power', 'rate at which work is done'],
                ['Energy', 'capacity to do work'],
                ['Kinetic energy', 'energy possessed by a moving body'],
                ['Potential energy', 'stored energy due to position or condition'],
                ['Pressure', 'force acting per unit area'],
                ['Density', 'mass per unit volume'],
                ['Current', 'rate of flow of electric charge'],
                ['Voltage', 'potential difference between two points'],
                ['Resistance', 'opposition to the flow of electric current'],
                ['Ohms law', 'the relation V equals IR in an electric circuit'],
                ['Frequency', 'number of complete waves per second'],
                ['Wavelength', 'distance between two corresponding points on a wave'],
                ['Reflection', 'bouncing back of light from a surface'],
                ['Refraction', 'bending of light as it passes between media'],
                ['Heat', 'energy transferred because of temperature difference'],
                ['Temperature', 'degree of hotness or coldness of a body'],
                ['Magnetism', 'force caused by magnets or moving electric charges'],
                ['Gravity', 'attractive force between masses'],
                ['Transformer', 'a device that changes alternating voltage'],
                ['Thermometer', 'an instrument used to measure temperature'],
                ['Telescope', 'an instrument used to view distant objects'],
                ['Stopwatch', 'a device used to measure short intervals of time'],
                ['Renewable energy', 'energy from sources that can be naturally replaced'],
                ['Fossil fuel', 'fuel formed from ancient organic matter'],
                ['Capacitor', 'a device used to store electric charge'],
                ['Diode', 'a component that allows current to flow mainly in one direction'],
                ['Fuse', 'a safety device that melts when current is too high'],
                ['Lever', 'a simple machine that turns about a pivot'],
                ['Echo', 'a reflected sound heard after a delay'],
            ]),
            'Lagos Street Knowledge' => $this->conceptQuestions('Lagos Street Knowledge', [
                ['Eyo festival', 'a traditional Lagos masquerade festival'],
                ['Third Mainland Bridge', 'a major bridge linking Lagos Island and the mainland'],
                ['Balogun Market', 'a major commercial market on Lagos Island'],
                ['National Theatre', 'a landmark cultural building in Iganmu'],
                ['Tafawa Balewa Square', 'a historic ceremonial ground in Lagos Island'],
                ['Makoko', 'a waterfront community in Lagos'],
                ['Lekki Conservation Centre', 'a nature reserve known for its canopy walkway'],
                ['BRT', 'a bus rapid transit system used for mass transport in Lagos'],
                ['Marina', 'a business district on Lagos Island'],
                ['Badagry', 'a historic coastal town in Lagos State'],
                ['Tinubu Square', 'a historic open space on Lagos Island'],
                ['Freedom Park', 'a cultural park built on the site of an old prison'],
                ['CMS', 'a busy transport and commercial area on Lagos Island'],
                ['Idumota', 'a Lagos Island district known for wholesale trading'],
                ['Oshodi', 'a major mainland transport hub'],
                ['Mile 12 Market', 'a large food market known for farm produce'],
                ['Computer Village', 'an Ikeja market known for phones and electronics'],
                ['Alaba International Market', 'a major electronics and appliance market'],
                ['Bar Beach', 'a former popular beachfront area on Victoria Island'],
                ['Elegushi Beach', 'a private beach in Lekki'],
                ['Ikoyi', 'an upscale area linked to offices and residences'],
                ['Yaba', 'a mainland area known for schools and technology activity'],
                ['Surulere', 'a mainland district known for sports and entertainment history'],
                ['Apapa Port', 'a major seaport area in Lagos'],
                ['Carter Bridge', 'one of the bridges linking Lagos Island to the mainland'],
            ]),
            'Nigeria and States' => $this->conceptQuestions('Nigeria and States', [
                ['Abuja', 'the capital city of Nigeria'],
                ['Lagos', 'Nigeria state known as a major commercial centre'],
                ['Kano', 'a major city in northern Nigeria known for trade and history'],
                ['Rivers State', 'a Niger Delta state with Port Harcourt as capital'],
                ['Calabar', 'the capital of Cross River State'],
                ['Enugu', 'a southeastern city historically linked with coal mining'],
                ['Ibadan', 'the capital of Oyo State'],
                ['Benin City', 'the capital of Edo State'],
                ['Kaduna', 'a major city in northwestern Nigeria'],
                ['Abeokuta', 'the capital of Ogun State'],
                ['Uyo', 'the capital of Akwa Ibom State'],
                ['Owerri', 'the capital of Imo State'],
                ['Lokoja', 'the capital of Kogi State'],
                ['Makurdi', 'the capital of Benue State'],
                ['Jalingo', 'the capital of Taraba State'],
                ['Gombe', 'the capital of Gombe State'],
                ['Birnin Kebbi', 'the capital of Kebbi State'],
                ['Sokoto', 'the capital of Sokoto State'],
                ['Yenagoa', 'the capital of Bayelsa State'],
                ['Asaba', 'the capital of Delta State'],
                ['Awka', 'the capital of Anambra State'],
                ['Ilorin', 'the capital of Kwara State'],
                ['Minna', 'the capital of Niger State'],
                ['Maiduguri', 'the capital of Borno State'],
                ['Damaturu', 'the capital of Yobe State'],
            ]),
            'Bible Quiz' => $this->conceptQuestions('Bible Quiz', [
                ['Genesis', 'the first book of the Bible'],
                ['Moses', 'the leader associated with the Exodus from Egypt'],
                ['David', 'the shepherd who became king of Israel'],
                ['Goliath', 'the giant defeated by David'],
                ['Bethlehem', 'the town associated with the birth of Jesus'],
                ['Noah', 'the man associated with building the ark'],
                ['Proverbs', 'a Bible book known for wisdom sayings'],
                ['Paul', 'an apostle who wrote many New Testament letters'],
                ['Jordan River', 'the river where Jesus was baptized'],
                ['Good Samaritan', 'a parable about showing mercy to a stranger'],
                ['Abraham', 'a patriarch associated with covenant and faith'],
                ['Sarah', 'the wife of Abraham and mother of Isaac'],
                ['Joseph', 'Jacob son who became powerful in Egypt'],
                ['Joshua', 'the leader who succeeded Moses'],
                ['Samson', 'a judge known for unusual strength'],
                ['Solomon', 'a king known for wisdom'],
                ['Elijah', 'a prophet associated with Mount Carmel'],
                ['Daniel', 'a faithful exile associated with the lions den'],
                ['Esther', 'a queen who helped save her people'],
                ['Mary', 'the mother of Jesus'],
                ['Peter', 'a disciple also called Simon Peter'],
                ['John the Baptist', 'the preacher who prepared the way for Jesus'],
                ['Pentecost', 'the event associated with the Holy Spirit coming on believers'],
                ['Beatitudes', 'teachings of Jesus that begin with blessed'],
                ['Prodigal Son', 'a parable about repentance and forgiveness'],
            ]),
            'Quran Quiz' => $this->conceptQuestions('Quran Quiz', [
                ['Al-Fatihah', 'the opening chapter of the Quran'],
                ['Surah', 'a chapter of the Quran'],
                ['Ayah', 'a verse of the Quran'],
                ['Ramadan', 'the month of fasting in Islam'],
                ['Makkah', 'the holy city where the Kaaba is located'],
                ['Madinah', 'the city associated with the Prophet migration'],
                ['Zakat', 'obligatory giving in Islam'],
                ['Hajj', 'pilgrimage to Makkah'],
                ['Salah', 'the regular prayer observed by Muslims'],
                ['Tawhid', 'belief in the oneness of Allah'],
                ['Sawm', 'fasting observed especially in Ramadan'],
                ['Shahadah', 'the declaration of faith in Islam'],
                ['Qiblah', 'the direction Muslims face during prayer'],
                ['Kaaba', 'the sacred house in Makkah toward which Muslims pray'],
                ['Jumuah', 'the Friday congregational prayer'],
                ['Hadith', 'reports of sayings and actions of the Prophet'],
                ['Sunnah', 'the prophetic example followed by Muslims'],
                ['Wudu', 'ablution performed before prayer'],
                ['Tafsir', 'explanation or interpretation of Quranic verses'],
                ['Imam', 'a leader of prayer or religious guide'],
                ['Eid al-Fitr', 'festival marking the end of Ramadan'],
                ['Eid al-Adha', 'festival associated with sacrifice during Hajj season'],
                ['Sadaqah', 'voluntary charity in Islam'],
                ['Hijrah', 'the migration from Makkah to Madinah'],
                ['Muezzin', 'the person who calls Muslims to prayer'],
            ]),
            'Yoruba Culture' => $this->conceptQuestions('Yoruba Culture', [
                ['Ile-Ife', 'a city often described as a cradle of Yoruba civilization'],
                ['Adire', 'a resist-dyed cloth associated with Yoruba textile art'],
                ['Talking drum', 'a drum that can imitate speech tones'],
                ['Aso Oke', 'a handwoven Yoruba fabric used for special occasions'],
                ['Oriki', 'praise poetry used to celebrate lineage and identity'],
                ['Oba', 'a traditional ruler in many Yoruba communities'],
                ['Ewedu', 'a leafy soup common in Yoruba cuisine'],
                ['Iro and Buba', 'traditional Yoruba clothing items'],
                ['Egungun', 'a masquerade tradition honoring ancestors'],
                ['Dundun', 'a family of Yoruba talking drums'],
                ['Ooni of Ife', 'a prominent traditional ruler linked with Ile-Ife'],
                ['Alaafin', 'a historic royal title associated with Oyo'],
                ['Gele', 'a head tie commonly worn for special occasions'],
                ['Agbada', 'a flowing traditional robe worn by men'],
                ['Gangan', 'an hourglass talking drum'],
                ['Bata drum', 'a Yoruba drum associated with dance and ceremony'],
                ['Ifa', 'a Yoruba divination system'],
                ['Owambe', 'a lively social party culture'],
                ['Amala', 'a Yoruba staple made from yam flour or cassava flour'],
                ['Gbegiri', 'a bean soup often served with amala'],
                ['Efo riro', 'a rich vegetable soup common in Yoruba cooking'],
                ['Ayo', 'a traditional board game played with seeds'],
                ['Ere Ibeji', 'twin figures in Yoruba art and belief'],
                ['Esusu', 'a rotating savings contribution system'],
                ['Prostration', 'a male greeting gesture of respect in Yoruba culture'],
            ]),
            'Igbo Culture' => $this->conceptQuestions('Igbo Culture', [
                ['New Yam Festival', 'a harvest celebration centered on yam'],
                ['Kola nut', 'a symbolic nut used in welcome and ceremony'],
                ['Ofo', 'a staff symbolizing authority and justice'],
                ['Uli', 'a traditional Igbo decorative art style'],
                ['Isiagu', 'a patterned outfit worn for special occasions'],
                ['Ichie', 'a title holder or elder in some Igbo communities'],
                ['Nze na Ozo', 'a respected title system in Igbo society'],
                ['Mmanwu', 'a masquerade tradition in Igbo communities'],
                ['Akwete cloth', 'a handwoven textile associated with Akwete'],
                ['Nsibidi', 'an ancient system of symbols used in southeastern Nigeria'],
                ['Ofala festival', 'a royal festival observed in some Igbo kingdoms'],
                ['Ogene', 'a metal gong used in music and announcements'],
                ['Ekwe', 'a wooden slit drum used for communication'],
                ['Oha soup', 'a soup made with oha leaves'],
                ['Ukwa', 'breadfruit eaten in several Igbo communities'],
                ['Palm wine', 'a traditional drink tapped from palm trees'],
                ['Umuada', 'daughters of a lineage or community'],
                ['Dibia', 'a traditional healer or diviner'],
                ['Age grade', 'an organized group of people born around the same period'],
                ['Iri Ji', 'the Igbo name for eating new yam ceremony'],
                ['Okpa', 'a food made from bambara nut flour'],
                ['Abacha', 'a cassava-based dish often called African salad'],
                ['Oji', 'the Igbo word for kola nut'],
                ['Afa', 'a divination practice in some Igbo communities'],
                ['Mbari', 'a traditional art form associated with Owerri area'],
            ]),
            'Hausa Culture' => $this->conceptQuestions('Hausa Culture', [
                ['Durbar', 'a colorful horse parade during major festivals'],
                ['Emir', 'a traditional ruler in many Hausa emirates'],
                ['Kano Emirate', 'one of the historic Hausa emirates'],
                ['Zaria', 'a historic city also known as Zazzau'],
                ['Dye pits', 'traditional indigo dyeing sites in Kano'],
                ['Hausa language', 'a widely spoken language in northern Nigeria'],
                ['Babban riga', 'a flowing robe worn by men'],
                ['Hula', 'a traditional cap worn by Hausa men'],
                ['Tuwo shinkafa', 'a rice-based swallow food'],
                ['Miyan kuka', 'a soup made from baobab leaves'],
                ['Suya', 'spiced grilled meat popular across Nigeria'],
                ['Kilishi', 'dried spiced meat'],
                ['Kunu', 'a traditional grain drink'],
                ['Fura da nono', 'millet balls served with fermented milk'],
                ['Sarki', 'a Hausa title for king or ruler'],
                ['Almajiri', 'a student in a traditional Islamic learning system'],
                ['Gidan Makama', 'a museum in Kano preserving history'],
                ['Dala Hill', 'a historic hill in Kano'],
                ['Gobir', 'a historic Hausa kingdom'],
                ['Katsina', 'a historic Hausa city and state'],
                ['Sallah celebration', 'festival celebration after major Islamic observances'],
                ['Gwari', 'a northern Nigerian ethnic group also called Gbagyi'],
                ['Arewa', 'a term commonly used for northern Nigeria'],
                ['Ranar Kasuwa', 'a market day in Hausa communities'],
                ['Kofar Mata', 'an old gate area known for dyeing in Kano'],
            ]),
            'Edo Heritage' => $this->conceptQuestions('Edo Heritage', [
                ['Benin Bronzes', 'historic artworks associated with the Benin Kingdom'],
                ['Oba of Benin', 'the traditional ruler of the Benin Kingdom'],
                ['Igue festival', 'a royal festival in Benin tradition'],
                ['Benin Moat', 'ancient earthworks around Benin City'],
                ['Igun Street', 'a Benin City area known for bronze casting'],
                ['Queen Idia', 'a historic Benin queen mother'],
                ['Ewuare the Great', 'a famous Oba credited with expanding Benin power'],
                ['Edo language', 'a language spoken by Edo people'],
                ['Ugie festival', 'a set of ceremonial festivals in Benin culture'],
                ['Brass casting', 'a craft strongly linked with Benin artistry'],
                ['Ekasa dance', 'a traditional royal dance in Benin culture'],
                ['Benin City', 'the capital of Edo State'],
                ['Ovia River', 'a river associated with Edo State'],
                ['Esan', 'an ethnic group in Edo Central'],
                ['Auchi', 'a major town in northern Edo State'],
                ['Uromi', 'a prominent town in Esan land'],
                ['Okada', 'a town in Edo State known for university activity'],
                ['Sapele Road', 'a major route in Benin City'],
                ['Ring Road', 'a central landmark area in Benin City'],
                ['Edo State', 'a state in southern Nigeria with Benin City as capital'],
                ['Guild system', 'organized craft groups in old Benin society'],
                ['Queen Mother', 'a royal title associated with the Iyoba'],
                ['Royal palace', 'the traditional seat of the Oba of Benin'],
                ['Coral beads', 'beads used in Benin royal dressing'],
                ['Ivory mask', 'a famous Benin artwork associated with Queen Idia'],
            ]),
            'World Football' => $this->conceptQuestions('World Football', [
                ['Penalty kick', 'a direct kick from the penalty spot'],
                ['Offside', 'an attacking position restricted by football rules'],
                ['Hat-trick', 'three goals scored by one player in a match'],
                ['Clean sheet', 'a match in which a team concedes no goal'],
                ['Corner kick', 'a restart awarded when defenders put the ball over their goal line'],
                ['Free kick', 'a restart awarded after certain fouls'],
                ['Yellow card', 'a caution shown by the referee'],
                ['Red card', 'a dismissal from the match'],
                ['Goalkeeper', 'the player allowed to handle the ball in the penalty area'],
                ['Striker', 'a forward player mainly expected to score goals'],
                ['Midfielder', 'a player linking defense and attack'],
                ['Defender', 'a player mainly responsible for stopping attacks'],
                ['VAR', 'video assistant referee technology'],
                ['Extra time', 'additional periods played after a draw in knockout games'],
                ['Penalty shootout', 'kicks used to decide some tied knockout matches'],
                ['Own goal', 'a goal accidentally scored against a player own team'],
                ['Substitution', 'replacing one player with another during a match'],
                ['Captain', 'the player who leads the team on the field'],
                ['Fixture', 'a scheduled match'],
                ['Derby', 'a match between rival teams from the same area'],
                ['Promotion', 'moving to a higher league after success'],
                ['Relegation', 'moving to a lower league after poor performance'],
                ['Trophy', 'an award given to competition winners'],
                ['Referee', 'the official who controls a football match'],
                ['Assistant referee', 'an official who helps judge offsides and throw-ins'],
            ]),
            'African Countries' => $this->conceptQuestions('African Countries', [
                ['Ghana', 'a West African country with Accra as capital'],
                ['Kenya', 'an East African country with Nairobi as capital'],
                ['Egypt', 'a North African country with Cairo as capital'],
                ['Morocco', 'a North African country with Rabat as capital'],
                ['Senegal', 'a West African country with Dakar as capital'],
                ['Cameroon', 'a Central African country with Yaounde as capital'],
                ['Tanzania', 'an East African country with Dodoma as capital'],
                ['Ethiopia', 'an East African country with Addis Ababa as capital'],
                ['Uganda', 'an East African country with Kampala as capital'],
                ['Rwanda', 'an East African country with Kigali as capital'],
                ['Zambia', 'a Southern African country with Lusaka as capital'],
                ['Zimbabwe', 'a Southern African country with Harare as capital'],
                ['Namibia', 'a Southern African country with Windhoek as capital'],
                ['Botswana', 'a Southern African country with Gaborone as capital'],
                ['Mali', 'a West African country with Bamako as capital'],
                ['Niger', 'a West African country with Niamey as capital'],
                ['Chad', 'a Central African country with N Djamena as capital'],
                ['Sudan', 'a Northeast African country with Khartoum as capital'],
                ['Somalia', 'an East African country with Mogadishu as capital'],
                ['Liberia', 'a West African country with Monrovia as capital'],
                ['Sierra Leone', 'a West African country with Freetown as capital'],
                ['Gambia', 'a West African country with Banjul as capital'],
                ['Angola', 'a Southern African country with Luanda as capital'],
                ['Mozambique', 'a Southern African country with Maputo as capital'],
                ['Madagascar', 'an island country with Antananarivo as capital'],
            ]),
        ];
    }

    private function englishQuestions(): array
    {
        $words = [
            ['brief', 'short', 'lengthy'],
            ['generous', 'kind', 'selfish'],
            ['ancient', 'old', 'modern'],
            ['brave', 'courageous', 'cowardly'],
            ['scarce', 'rare', 'abundant'],
            ['rapid', 'fast', 'slow'],
            ['silent', 'quiet', 'noisy'],
            ['expand', 'increase', 'reduce'],
            ['honest', 'truthful', 'dishonest'],
            ['fragile', 'delicate', 'strong'],
            ['accurate', 'correct', 'incorrect'],
            ['vacant', 'empty', 'occupied'],
            ['purchase', 'buy', 'sell'],
            ['assist', 'help', 'hinder'],
            ['reject', 'refuse', 'accept'],
            ['gloomy', 'dull', 'cheerful'],
            ['difficult', 'hard', 'easy'],
            ['commence', 'begin', 'end'],
            ['tiny', 'small', 'huge'],
            ['wealthy', 'rich', 'poor'],
            ['calm', 'peaceful', 'agitated'],
            ['frequent', 'often', 'rare'],
            ['permit', 'allow', 'forbid'],
            ['repair', 'fix', 'damage'],
            ['observe', 'watch', 'ignore'],
            ['bright', 'brilliant', 'dull'],
            ['careful', 'cautious', 'careless'],
            ['complex', 'complicated', 'simple'],
            ['defend', 'protect', 'attack'],
            ['depart', 'leave', 'arrive'],
            ['eager', 'keen', 'reluctant'],
            ['famous', 'well-known', 'unknown'],
            ['fortunate', 'lucky', 'unlucky'],
            ['genuine', 'real', 'fake'],
            ['humid', 'damp', 'dry'],
            ['include', 'contain', 'exclude'],
            ['joyful', 'happy', 'sad'],
            ['loyal', 'faithful', 'disloyal'],
            ['major', 'important', 'minor'],
            ['near', 'close', 'far'],
            ['ordinary', 'common', 'unusual'],
            ['patient', 'calm', 'impatient'],
            ['precise', 'exact', 'vague'],
            ['private', 'personal', 'public'],
            ['safe', 'secure', 'dangerous'],
            ['simple', 'easy', 'complex'],
            ['strong', 'powerful', 'weak'],
            ['temporary', 'brief', 'permanent'],
            ['victory', 'success', 'defeat'],
            ['visible', 'seen', 'hidden'],
            ['wisdom', 'knowledge', 'foolishness'],
            ['youthful', 'young', 'old'],
            ['zealous', 'enthusiastic', 'indifferent'],
            ['beneficial', 'helpful', 'harmful'],
            ['combine', 'join', 'separate'],
            ['create', 'make', 'destroy'],
        ];

        $questions = [];
        foreach ($words as $index => [$word, $meaning, $opposite]) {
            $questions[] = $this->makeQuestion(
                'Choose the word nearest in meaning to "' . $word . '".',
                $meaning,
                $this->neighborValues($words, $index, 1),
                $index
            );

            $questions[] = $this->makeQuestion(
                'Choose the word opposite in meaning to "' . $word . '".',
                $opposite,
                $this->neighborValues($words, $index, 2),
                $index + 1
            );
        }

        return array_merge(
            $questions,
            $this->comprehensionQuestions(),
            $this->conceptQuestions('English Language', [
                ['Dictionary', 'a resource that gives word meanings'],
                ['Library', 'a place where books and information resources are kept'],
                ['Communication', 'the exchange of information between people'],
                ['Sentence', 'a group of words that expresses a complete thought'],
                ['Paragraph', 'a group of sentences about one main idea'],
                ['Grammar', 'the rules that guide correct language use'],
            ])
        );
    }

    private function geographyQuestions(): array
    {
        $capitals = [
            ['France', 'Paris'],
            ['Egypt', 'Cairo'],
            ['Nigeria', 'Abuja'],
            ['Ghana', 'Accra'],
            ['Kenya', 'Nairobi'],
            ['South Africa', 'Pretoria'],
            ['Brazil', 'Brasilia'],
            ['Canada', 'Ottawa'],
            ['Japan', 'Tokyo'],
            ['China', 'Beijing'],
            ['India', 'New Delhi'],
            ['Germany', 'Berlin'],
            ['Italy', 'Rome'],
            ['Spain', 'Madrid'],
            ['Portugal', 'Lisbon'],
            ['Australia', 'Canberra'],
            ['Argentina', 'Buenos Aires'],
            ['United States', 'Washington DC'],
            ['United Kingdom', 'London'],
            ['Russia', 'Moscow'],
            ['Ethiopia', 'Addis Ababa'],
            ['Morocco', 'Rabat'],
            ['Senegal', 'Dakar'],
            ['Cameroon', 'Yaounde'],
            ['Tanzania', 'Dodoma'],
            ['Norway', 'Oslo'],
            ['Sweden', 'Stockholm'],
            ['Denmark', 'Copenhagen'],
            ['Finland', 'Helsinki'],
            ['Iceland', 'Reykjavik'],
            ['Ireland', 'Dublin'],
            ['Netherlands', 'Amsterdam'],
            ['Belgium', 'Brussels'],
            ['Switzerland', 'Bern'],
            ['Austria', 'Vienna'],
            ['Poland', 'Warsaw'],
            ['Ukraine', 'Kyiv'],
            ['Turkey', 'Ankara'],
            ['Greece', 'Athens'],
            ['Saudi Arabia', 'Riyadh'],
            ['United Arab Emirates', 'Abu Dhabi'],
            ['Qatar', 'Doha'],
            ['Jordan', 'Amman'],
            ['Lebanon', 'Beirut'],
            ['Iraq', 'Baghdad'],
            ['Iran', 'Tehran'],
            ['Pakistan', 'Islamabad'],
            ['Bangladesh', 'Dhaka'],
            ['Indonesia', 'Jakarta'],
            ['Malaysia', 'Kuala Lumpur'],
            ['Singapore', 'Singapore'],
            ['Thailand', 'Bangkok'],
            ['Vietnam', 'Hanoi'],
            ['Philippines', 'Manila'],
            ['South Korea', 'Seoul'],
            ['North Korea', 'Pyongyang'],
            ['Mexico', 'Mexico City'],
            ['Chile', 'Santiago'],
            ['Peru', 'Lima'],
            ['Colombia', 'Bogota'],
            ['Venezuela', 'Caracas'],
            ['Cuba', 'Havana'],
            ['Jamaica', 'Kingston'],
            ['New Zealand', 'Wellington'],
            ['Algeria', 'Algiers'],
            ['Tunisia', 'Tunis'],
            ['Libya', 'Tripoli'],
            ['Uganda', 'Kampala'],
            ['Rwanda', 'Kigali'],
            ['Zambia', 'Lusaka'],
            ['Zimbabwe', 'Harare'],
            ['Namibia', 'Windhoek'],
            ['Botswana', 'Gaborone'],
            ['Angola', 'Luanda'],
            ['Mozambique', 'Maputo'],
            ['Madagascar', 'Antananarivo'],
            ['Mali', 'Bamako'],
            ['Niger', 'Niamey'],
            ['Chad', 'N Djamena'],
        ];

        $questions = [];
        foreach ($capitals as $index => [$country, $capital]) {
            $questions[] = $this->makeQuestion(
                'What is the capital of ' . $country . '?',
                $capital,
                $this->neighborValues($capitals, $index, 1),
                $index
            );

            $questions[] = $this->makeQuestion(
                $capital . ' is the capital of which country?',
                $country,
                $this->neighborValues($capitals, $index, 0),
                $index + 1
            );
        }

        return array_merge(
            $questions,
            $this->conceptQuestions('Geography', [
                ['Compass', 'an instrument used to find direction'],
                ['Atlas', 'a book or collection of maps'],
                ['Calendar', 'a system for organizing days, weeks, and months'],
                ['Population', 'the total number of people living in an area'],
                ['Agriculture', 'the practice of cultivating crops and rearing animals'],
                ['Transportation', 'the movement of people or goods from place to place'],
                ['Latitude', 'distance north or south of the equator measured in degrees'],
                ['Longitude', 'distance east or west of the prime meridian measured in degrees'],
            ])
        );
    }

    private function comprehensionQuestions(): array
    {
        $marketPassage = 'Passage: A morning market opened early. Traders arranged food, students hurried to school, and officers guided traffic when the narrow road became busy.';
        $libraryPassage = 'Passage: Amara joined a reading club to improve her vocabulary. Each Friday, members discussed a story and wrote a summary.';

        return [
            $this->makeQuestion($marketPassage . ' What opened early?', 'The market.', ['The school.', 'The council office.', 'The library.'], 1),
            $this->makeQuestion($marketPassage . ' Who hurried to school?', 'Students.', ['Officers.', 'Traders.', 'Drivers.'], 2),
            $this->makeQuestion($marketPassage . ' Why did officers guide traffic?', 'The narrow road became busy.', ['The market was closed.', 'Students were absent.', 'The food was sold out.'], 3),
            $this->makeQuestion($marketPassage . ' The word "hurried" means ____.', 'moved quickly', ['slept deeply', 'spoke softly', 'waited calmly'], 4),
            $this->makeQuestion($marketPassage . ' What is the main idea?', 'A busy market morning needed order.', ['A school cancelled classes.', 'A library opened late.', 'Officers sold food.'], 5),
            $this->makeQuestion($libraryPassage . ' Why did Amara join the club?', 'To improve her vocabulary.', ['To sell books.', 'To avoid school.', 'To learn traffic rules.'], 6),
            $this->makeQuestion($libraryPassage . ' When did the members meet?', 'Each Friday.', ['Every morning.', 'Once a year.', 'Only on holidays.'], 7),
            $this->makeQuestion($libraryPassage . ' What did members write?', 'A summary.', ['A traffic report.', 'A shopping list.', 'A timetable.'], 8),
            $this->makeQuestion($libraryPassage . ' What did members discuss?', 'A story.', ['A road map.', 'A football match.', 'A market price.'], 9),
            $this->makeQuestion($libraryPassage . ' Which title best fits?', 'Amara and the Reading Club', ['The Busy Road', 'A Day at the Market', 'The Lost Calculator'], 10),
        ];
    }

    private function mathematicsQuestions(): array
    {
        return array_merge([
            $this->makeQuestion('Solve for x: 3x + 7 = 22.', '5', ['4', '6', '7'], 1),
            $this->makeQuestion('Factorize x^2 - 5x + 6.', '(x - 2)(x - 3)', ['(x + 2)(x + 3)', '(x - 1)(x - 6)', '(x + 1)(x - 6)'], 2),
            $this->makeQuestion('Expand (2x - 3)(x + 4).', '2x^2 + 5x - 12', ['2x^2 - 5x - 12', '2x^2 + 8x - 3', '2x^2 + 5x + 12'], 3),
            $this->makeQuestion('If f(x) = 2x^2 - 3x, find f(4).', '20', ['16', '24', '28'], 4),
            $this->makeQuestion('Find the slope of the line through (2, 3) and (6, 11).', '2', ['1', '3', '4'], 5),
            $this->makeQuestion('Find the equation of the line with slope 3 passing through (1, 2).', 'y = 3x - 1', ['y = 3x + 1', 'y = x + 3', 'y = 2x + 3'], 6),
            $this->makeQuestion('Solve simultaneously: x + y = 10 and x - y = 2.', 'x = 6, y = 4', ['x = 4, y = 6', 'x = 5, y = 5', 'x = 8, y = 2'], 7),
            $this->makeQuestion('Solve simultaneously: 2x - y = 7 and x + y = 5.', 'x = 4, y = 1', ['x = 3, y = 2', 'x = 5, y = 0', 'x = 2, y = 3'], 8),
            $this->makeQuestion('What is the sum of the interior angles of a hexagon?', '720 degrees', ['540 degrees', '600 degrees', '900 degrees'], 9),
            $this->makeQuestion('Using pi = 22/7, find the area of a circle with radius 7 cm.', '154 cm^2', ['44 cm^2', '77 cm^2', '308 cm^2'], 10),
            $this->makeQuestion('Find the volume of a cuboid measuring 4 cm by 5 cm by 6 cm.', '120 cm^3', ['90 cm^3', '100 cm^3', '150 cm^3'], 11),
            $this->makeQuestion('A right triangle has legs 9 cm and 12 cm. Find the hypotenuse.', '15 cm', ['14 cm', '16 cm', '21 cm'], 12),
            $this->makeQuestion('Simplify 2^3 x 2^4.', '2^7', ['2^12', '4^7', '2^1'], 13),
            $this->makeQuestion('Simplify a^5 / a^2.', 'a^3', ['a^7', 'a^10', 'a^2'], 14),
            $this->makeQuestion('Find log10(1000).', '3', ['2', '10', '100'], 15),
            $this->makeQuestion('Evaluate sqrt(144) + 3^2.', '21', ['15', '18', '24'], 16),
            $this->makeQuestion('Solve x^2 - 9 = 0.', 'x = 3 or x = -3', ['x = 9 only', 'x = 0 or x = 9', 'x = -9 only'], 17),
            $this->makeQuestion('Find the roots of x^2 - 7x + 10 = 0.', '2 and 5', ['1 and 10', '3 and 4', '-2 and -5'], 18),
            $this->makeQuestion('Find the discriminant of x^2 - 4x + 4 = 0.', '0', ['4', '8', '16'], 19),
            $this->makeQuestion('Find the median of 3, 7, 9, 12, 14.', '9', ['7', '10', '12'], 20),
            $this->makeQuestion('Find the mean of 4, 6, 8, and 10.', '7', ['6', '8', '9'], 21),
            $this->makeQuestion('What is the probability of rolling an even number on a fair die?', '1/2', ['1/3', '2/3', '1/6'], 22),
            $this->makeQuestion('Find the simple interest on 500 at 10 percent per year for 2 years.', '100', ['50', '75', '150'], 23),
            $this->makeQuestion('Find 30 percent of 250.', '75', ['60', '70', '85'], 24),
            $this->makeQuestion('If y varies directly as x and y = 12 when x = 4, find y when x = 7.', '21', ['18', '24', '28'], 25),
            $this->makeQuestion('Find the 10th term of the arithmetic sequence 5, 9, 13, ...', '41', ['37', '45', '49'], 26),
            $this->makeQuestion('Find the next term of the geometric sequence 3, 6, 12, ...', '24', ['18', '21', '30'], 27),
            $this->makeQuestion('Find the sum of the first five terms of 2, 5, 8, 11, ...', '40', ['35', '45', '50'], 28),
            $this->makeQuestion('Find the determinant of the matrix [[2, 3], [1, 4]].', '5', ['7', '8', '11'], 29),
            $this->makeQuestion('If A = [[1, 2], [3, 4]], what is the trace of A?', '5', ['4', '6', '10'], 30),
            $this->makeQuestion('What is sin 30 degrees?', '1/2', ['0', '1', 'sqrt(3)/2'], 31),
            $this->makeQuestion('What is cos 60 degrees?', '1/2', ['0', '1', 'sqrt(3)/2'], 32),
            $this->makeQuestion('What is tan 45 degrees?', '1', ['0', '1/2', 'sqrt(3)'], 33),
            $this->makeQuestion('Convert 3/8 to a decimal.', '0.375', ['0.25', '0.35', '0.625'], 34),
            $this->makeQuestion('Solve |x| = 6.', 'x = 6 or x = -6', ['x = 6 only', 'x = -6 only', 'x = 0 or x = 6'], 35),
            $this->makeQuestion('Expand (x + 2)^2.', 'x^2 + 4x + 4', ['x^2 + 2x + 4', 'x^2 + 4', 'x^2 + 2x + 2'], 36),
            $this->makeQuestion('Rationalize 1 / sqrt(2).', 'sqrt(2) / 2', ['sqrt(2)', '2 / sqrt(2)', '1 / 2'], 37),
            $this->makeQuestion('Write 0.0045 in standard form.', '4.5 x 10^-3', ['4.5 x 10^3', '45 x 10^-3', '0.45 x 10^-2'], 38),
            $this->makeQuestion('Find the gradient of y = -2x + 5.', '-2', ['2', '5', '-5'], 39),
            $this->makeQuestion('Find the y-intercept of y = 4x - 7.', '-7', ['4', '7', '-4'], 40),
            $this->makeQuestion('Find the x-intercept of 2x + 4 = 0.', '-2', ['2', '4', '-4'], 41),
            $this->makeQuestion('Find the perimeter of a rectangle of length 12 cm and width 5 cm.', '34 cm', ['17 cm', '24 cm', '60 cm'], 42),
            $this->makeQuestion('Evaluate 5!.', '120', ['20', '60', '100'], 43),
            $this->makeQuestion('Evaluate 5C2.', '10', ['5', '15', '20'], 44),
            $this->makeQuestion('If A = {1, 2, 3} and B = {3, 4}, find A intersection B.', '{3}', ['{1, 2}', '{4}', '{1, 2, 3, 4}'], 45),
            $this->makeQuestion('What is sin^2(theta) + cos^2(theta)?', '1', ['0', 'sin(theta)', 'cos(theta)'], 46),
            $this->makeQuestion('Solve 4(x - 2) = 20.', '7', ['5', '6', '8'], 47),
            $this->makeQuestion('Convert binary 1010 to decimal.', '10', ['8', '9', '12'], 48),
            $this->makeQuestion('Find the LCM of 12 and 18.', '36', ['6', '24', '72'], 49),
            $this->makeQuestion('Find the HCF of 24 and 36.', '12', ['6', '18', '24'], 50),
            $this->makeQuestion('Differentiate y = 3x^2 + 2x - 5 with respect to x.', '6x + 2', ['3x + 2', '6x - 5', 'x^3 + x^2 - 5x'], 51),
            $this->makeQuestion('Integrate 4x^3 with respect to x.', 'x^4 + C', ['12x^2 + C', '4x^4 + C', 'x^3 + C'], 52),
            $this->makeQuestion('Find the inverse of f(x) = 2x + 3.', '(x - 3) / 2', ['(x + 3) / 2', '2x - 3', '3x + 2'], 53),
            $this->makeQuestion('If a sequence has nth term 3n - 1, find the 8th term.', '23', ['21', '24', '25'], 54),
            $this->makeQuestion('Find the sum to infinity of 8 + 4 + 2 + ...', '16', ['12', '20', '32'], 55),
            $this->makeQuestion('Solve the inequality 2x + 3 > 11.', 'x > 4', ['x < 4', 'x > 7', 'x < 7'], 56),
            $this->makeQuestion('Find the variance of 2, 4, and 6.', '8/3', ['2', '4/3', '4'], 57),
            $this->makeQuestion('Find the distance between (1, 2) and (4, 6).', '5', ['4', '6', '7'], 58),
            $this->makeQuestion('Convert 225 degrees to radians.', '5pi/4', ['3pi/4', '7pi/4', '2pi/3'], 59),
            $this->makeQuestion('Simplify (x^2 - 9) / (x - 3).', 'x + 3', ['x - 3', 'x^2 + 3', 'x + 9'], 60),
        ], $this->generatedMathematicsQuestions());
    }

    private function generatedMathematicsQuestions(): array
    {
        $questions = [];
        $offset = 100;

        for ($i = 1; $i <= 25; $i++) {
            $coefficient = ($i % 5) + 2;
            $answer = $i + 3;
            $constant = $i + 4;
            $result = ($coefficient * $answer) + $constant;

            $questions[] = $this->makeQuestion(
                'Solve for x: ' . $coefficient . 'x + ' . $constant . ' = ' . $result . '.',
                (string) $answer,
                [(string) ($answer - 1), (string) ($answer + 1), (string) ($answer + 2)],
                $offset++
            );
        }

        for ($i = 1; $i <= 20; $i++) {
            $principal = 200 + ($i * 50);
            $rate = 5 + ($i % 6);
            $time = ($i % 4) + 1;
            $interest = (string) (($principal * $rate * $time) / 100);

            $questions[] = $this->makeQuestion(
                'Find the simple interest on ' . $principal . ' at ' . $rate . ' percent per year for ' . $time . ' years.',
                $interest,
                [(string) ((float) $interest + 10), (string) ((float) $interest + 25), (string) max(1, (float) $interest - 15)],
                $offset++
            );
        }

        for ($i = 1; $i <= 20; $i++) {
            $length = $i + 6;
            $width = ($i % 8) + 3;
            $area = $length * $width;

            $questions[] = $this->makeQuestion(
                'Find the area of a rectangle with length ' . $length . ' cm and width ' . $width . ' cm.',
                $area . ' cm^2',
                [($area + $length) . ' cm^2', ($area + $width) . ' cm^2', (($length + $width) * 2) . ' cm^2'],
                $offset++
            );
        }

        for ($i = 1; $i <= 20; $i++) {
            $first = ($i % 7) + 2;
            $difference = ($i % 5) + 2;
            $term = ($i % 8) + 6;
            $answer = $first + (($term - 1) * $difference);

            $questions[] = $this->makeQuestion(
                'Find the ' . $term . 'th term of the arithmetic sequence with first term ' . $first . ' and common difference ' . $difference . '.',
                (string) $answer,
                [(string) ($answer - $difference), (string) ($answer + $difference), (string) ($answer + $difference + $first)],
                $offset++
            );
        }

        for ($i = 1; $i <= 20; $i++) {
            $percent = 5 * (($i % 10) + 1);
            $number = 80 + ($i * 20);
            $answer = ($percent * $number) / 100;

            $questions[] = $this->makeQuestion(
                'Find ' . $percent . ' percent of ' . $number . '.',
                (string) $answer,
                [(string) ($answer + 5), (string) ($answer + 10), (string) max(1, $answer - 5)],
                $offset++
            );
        }

        return $questions;
    }

    private function conceptQuestions(string $subject, array $concepts): array
    {
        $questions = [];

        foreach ($concepts as $index => [$term, $definition]) {
            $nearbyTerms = $this->neighborValues($concepts, $index, 0);
            $nearbyDefinitions = array_map(
                fn ($value) => ucfirst($value) . '.',
                $this->neighborValues($concepts, $index, 1)
            );
            $definitionAnswer = ucfirst($definition) . '.';

            $questions[] = $this->makeQuestion(
                'In ' . $subject . ', which term means ' . $definition . '?',
                $term,
                $nearbyTerms,
                $index
            );

            $questions[] = $this->makeQuestion(
                'In ' . $subject . ', which statement best describes ' . $term . '?',
                $definitionAnswer,
                $nearbyDefinitions,
                $index + 1
            );

            $questions[] = $this->makeQuestion(
                'In ' . $subject . ', the clue "' . $definition . '" points to which answer?',
                $term,
                $nearbyTerms,
                $index + 2
            );

            $questions[] = $this->makeQuestion(
                'A student reviewing ' . $subject . ' sees "' . $term . '". What does it mean?',
                $definitionAnswer,
                $nearbyDefinitions,
                $index + 3
            );

            $questions[] = $this->makeQuestion(
                'Which ' . $subject . ' idea is most closely linked with ' . $definition . '?',
                $term,
                $nearbyTerms,
                $index + 4
            );

            $questions[] = $this->makeQuestion(
                'In ' . $subject . ', what is most closely linked to ' . $term . '?',
                $definitionAnswer,
                $nearbyDefinitions,
                $index + 5
            );
        }

        return $questions;
    }

    private function ensureMinimumQuestions(string $subjectName, array $questions): array
    {
        $questions = $this->uniqueQuestionRows($questions);

        if (count($questions) >= self::MIN_QUESTIONS_PER_BANK) {
            return $questions;
        }

        $answerPool = [];

        foreach ($questions as $question) {
            $answerKey = $question['answer'] ?? null;
            $answer = $answerKey ? ($question[$answerKey] ?? null) : null;

            if (is_string($answer) && trim($answer) !== '') {
                $answerPool[] = $answer;
            }
        }

        $answerPool = array_values(array_unique($answerPool));

        if (count($answerPool) < 4) {
            throw new \RuntimeException('Question bank needs more answer variety before it can be expanded: ' . $subjectName);
        }

        $existingPrompts = array_fill_keys(array_column($questions, 'question'), true);
        $sourceQuestions = $questions;
        $serial = 1;

        while (count($questions) < self::MIN_QUESTIONS_PER_BANK) {
            $source = $sourceQuestions[($serial - 1) % count($sourceQuestions)];
            $answerKey = $source['answer'] ?? null;
            $correct = $answerKey ? ($source[$answerKey] ?? null) : null;

            if (! is_string($correct) || trim($correct) === '') {
                $serial++;
                continue;
            }

            $prompt = $subjectName . ' bank drill ' . $serial . ': which option correctly answers "' . Str::limit($source['question'], 105, '...') . '"?';

            if (isset($existingPrompts[$prompt])) {
                $serial++;
                continue;
            }

            $questions[] = $this->makeQuestion(
                $prompt,
                $correct,
                $this->rotatedWrongOptions($answerPool, $correct, $serial),
                $serial
            );
            $existingPrompts[$prompt] = true;
            $serial++;

            if ($serial > 1000) {
                throw new \RuntimeException('Could not safely expand question bank: ' . $subjectName);
            }
        }

        return $questions;
    }

    private function uniqueQuestionRows(array $questions): array
    {
        $seen = [];
        $unique = [];

        foreach ($questions as $question) {
            $prompt = $question['question'];

            if (isset($seen[$prompt])) {
                continue;
            }

            $seen[$prompt] = true;
            $unique[] = $question;
        }

        return $unique;
    }

    private function rotatedWrongOptions(array $options, string $correct, int $offset): array
    {
        $wrongOptions = array_values(array_filter(
            $options,
            fn ($option) => $option !== $correct
        ));

        if (count($wrongOptions) < 3) {
            throw new \RuntimeException('Question needs at least three wrong options for answer: ' . $correct);
        }

        $rotated = [];

        for ($step = 0; count($rotated) < 3; $step++) {
            $rotated[] = $wrongOptions[($offset + $step) % count($wrongOptions)];
        }

        return $rotated;
    }

    private function assertUniqueQuestionText(string $subjectName, array $questions): void
    {
        $seen = [];

        foreach ($questions as $question) {
            $prompt = $question['question'];

            if (isset($seen[$prompt])) {
                throw new \RuntimeException('Duplicate generated question in ' . $subjectName . ': ' . $prompt);
            }

            $seen[$prompt] = true;
        }
    }

    private function saveQuestions(Subject $subject, array $questions): void
    {
        $now = now();
        $prompts = array_column($questions, 'question');
        $existingQuestionIds = Question::whereIn('question', $prompts)->pluck('id', 'question');
        $updates = [];
        $inserts = [];

        foreach ($questions as $question) {
            $row = array_merge($question, [
                'subject_id' => $subject->id,
                'updated_at' => $now,
            ]);

            $existingId = $existingQuestionIds[$question['question']] ?? null;

            if ($existingId) {
                $updates[] = array_merge(['id' => $existingId], $row);
                continue;
            }

            $inserts[] = array_merge($row, [
                'created_at' => $now,
            ]);
        }

        foreach (array_chunk($updates, 250) as $chunk) {
            DB::table('questions')->upsert(
                $chunk,
                ['id'],
                ['subject_id', 'question', 'option_a', 'option_b', 'option_c', 'option_d', 'answer', 'duration', 'updated_at']
            );
        }

        foreach (array_chunk($inserts, 250) as $chunk) {
            DB::table('questions')->insert($chunk);
        }
    }

    private function makeQuestion(string $question, string $correct, array $wrongOptions, int $offset): array
    {
        $options = array_values(array_unique(array_merge([$correct], $wrongOptions)));

        if (count($options) < 4) {
            throw new \RuntimeException('Question needs at least four unique options: ' . $question);
        }

        $options = array_slice($options, 0, 4);
        $shift = $offset % 4;

        for ($i = 0; $i < $shift; $i++) {
            $options[] = array_shift($options);
        }

        $answerIndex = array_search($correct, $options, true);

        return [
            'question' => $question,
            'option_a' => $options[0],
            'option_b' => $options[1],
            'option_c' => $options[2],
            'option_d' => $options[3],
            'answer' => 'option_' . ['a', 'b', 'c', 'd'][$answerIndex],
            'duration' => self::DURATION_SECONDS,
        ];
    }

    private function neighborValues(array $items, int $index, int $column): array
    {
        $values = [];
        $count = count($items);

        for ($step = 1; count($values) < 3 && $step < $count; $step++) {
            $values[] = $items[($index + $step) % $count][$column];
        }

        return $values;
    }

    private function moveGeographyQuestions(): void
    {
        $geography = $this->subjectFor('Geography');

        Question::whereIn('question', [
            'What is the capital of France?',
            'Which city is the capital of France?',
        ])->update(['subject_id' => $geography->id]);
    }

    private function removeDuplicateQuestion(string $questionText): void
    {
        Question::where('question', $questionText)
            ->orderBy('id')
            ->get()
            ->skip(1)
            ->each
            ->delete();
    }

    private function removeDuplicateQuestions(): void
    {
        Question::select('question')
            ->groupBy('question')
            ->havingRaw('COUNT(*) > 1')
            ->pluck('question')
            ->each(fn ($questionText) => $this->removeDuplicateQuestion($questionText));
    }

    private function removeRetiredGeneralQuestions(): void
    {
        foreach ($this->retiredGeneralConcepts() as [$term, $definition]) {
            Question::whereIn('question', [
                'In General Knowledge, which term means ' . $definition . '?',
                'Which statement best describes ' . $term . '?',
            ])->delete();
        }
    }

    private function removeOldSampleQuestions(): void
    {
        Question::whereIn('question', [
            'Is this final project',
            'Is this the final project',
        ])->delete();
    }

    private function deactivateEmptyGeneralSubject(): void
    {
        $general = Subject::whereRaw('LOWER(name) = ?', ['general'])->first();

        if ($general && ! $general->questions()->exists()) {
            $general->update(['active' => false]);
        }
    }

    private function retiredGeneralConcepts(): array
    {
        return [
            ['Thermometer', 'an instrument used to measure temperature'],
            ['Compass', 'an instrument used to find direction'],
            ['Calendar', 'a system for organizing days, weeks, and months'],
            ['Dictionary', 'a book or resource that gives word meanings'],
            ['Atlas', 'a book or collection of maps'],
            ['Library', 'a place where books and information resources are kept'],
            ['Microscope', 'an instrument used to view very small objects'],
            ['Telescope', 'an instrument used to view distant objects'],
            ['Stopwatch', 'a device used to measure short intervals of time'],
            ['Calculator', 'a device used to perform mathematical operations'],
            ['Keyboard', 'an input device with keys for typing'],
            ['Printer', 'an output device that produces hard copies'],
            ['Recycling', 'processing used materials so they can be used again'],
            ['Hygiene', 'practices that help maintain health and cleanliness'],
            ['First aid', 'immediate help given to an injured or sick person'],
            ['Road signs', 'symbols that guide and warn road users'],
            ['Traffic light', 'a signal device that controls road movement'],
            ['Renewable energy', 'energy from sources that can be naturally replaced'],
            ['Fossil fuel', 'fuel formed from ancient organic matter'],
            ['Citizenship', 'membership of a country with rights and duties'],
            ['Population', 'the total number of people living in an area'],
            ['Culture', 'the way of life of a group of people'],
            ['Communication', 'the exchange of information between people'],
            ['Agriculture', 'the practice of cultivating crops and rearing animals'],
            ['Transportation', 'the movement of people or goods from place to place'],
        ];
    }

    private function subjectFor(string $name): Subject
    {
        $subject = Subject::whereRaw('LOWER(name) = ?', [Str::lower($name)])->first();
        $bankType = $this->bankTypeFor($name);

        if ($subject) {
            $subject->update([
                'active' => true,
                'bank_type' => $bankType,
            ]);

            return $subject;
        }

        return Subject::create([
            'name' => $name,
            'slug' => $this->makeUniqueSlug($name),
            'bank_type' => $bankType,
            'active' => true,
        ]);
    }

    private function bankTypeFor(string $name): string
    {
        return in_array($name, [
            'Lagos Street Knowledge',
            'Nigeria and States',
            'Bible Quiz',
            'Quran Quiz',
            'Yoruba Culture',
            'Igbo Culture',
            'Hausa Culture',
            'Edo Heritage',
            'World Football',
            'African Countries',
        ], true) ? Subject::TYPE_CHALLENGE : Subject::TYPE_ACADEMIC;
    }

    private function makeUniqueSlug(string $name): string
    {
        $baseSlug = Str::slug($name) ?: 'subject';
        $slug = $baseSlug;
        $suffix = 2;

        while (Subject::where('slug', $slug)->exists()) {
            $slug = $baseSlug . '-' . $suffix;
            $suffix++;
        }

        return $slug;
    }
}
