<?php

namespace App\Services;

class LagosQuestionBank
{
    // Short, individually written prompts. Research links: database/data/lagos-sources.json.
    public function rows(): array
    {
        $questions = [
            ["Where's Computer Village?", 'Ikeja', 'Ikoyi', 'Epe', 'Surulere'],
            ['Alaba International Market is best known for what?', 'Electronics', 'Fresh fish', 'Timber', 'Livestock'],
            ['Need tokunbo car parts. Which market?', 'Ladipo', 'Balogun', 'Mile 12', 'Tejuosho'],
            ['Katangua is famous for what?', 'Second-hand clothes', 'Fresh seafood', 'Car engines', 'Building sand'],
            ['Which bus stop is Katangua Market known by?', 'Super', 'Obalende', 'CMS', 'Jibowu'],
            ["Where's Tejuosho Market?", 'Yaba', 'Badagry', 'Epe', 'Ikoyi'],
            ['What is Mile 12 Market best known for?', 'Fresh farm produce', 'Phones', 'Car spare parts', 'Jewellery'],
            ['Oyingbo Market is in which mainland area?', 'Ebute Metta', 'Agege', 'Ikeja', 'Ogba'],
            ['Balogun Market is on which island?', 'Lagos Island', 'Victoria Island', 'Banana Island', 'Snake Island'],
            ["Alaba International Market is in which area?", 'Ojo', 'Yaba', 'Epe', 'Ikeja'],
            ["What did Oko-Baba's waterfront trade mainly deal in?", 'Timber', 'Phones', 'Gold', 'Leather'],
            ['Which expressway leads to Lagos Trade Fair Complex?', 'Lagos–Badagry', 'Lekki–Epe', 'Lagos–Ibadan', 'Apapa–Oshodi'],
            ["What's the name of Lagos's yellow minibuses?", 'Danfo', 'Keke', 'Okada', 'Taxi'],
            ["What's a molue?", 'A large commercial bus', 'A canoe', 'A motorcycle', 'A tricycle'],
            ["In a danfo, 'owo da?' means what?", "Where's your fare?", "Where's your bag?", 'Which bus stop?', 'Move your seat'],
            ["What does a conductor mean by 'shole'?", 'Get down', 'Pay twice', 'Move to the back', 'Close the window'],
            ["'Enter with your change' means what?", 'Have the exact fare ready', 'Change your clothes', 'Swap buses', 'Bring a spare tyre'],
            ["'No lap' means what in a danfo?", "No sitting on another passenger's lap", 'No luggage', 'No night trips', 'No round trips'],
            ["A conductor shouts 'CMS!' Which part of Lagos?", 'Lagos Island', 'Agege', 'Badagry', 'Ikorodu'],
            ['Which card works on Lagos BRT and rail?', 'Cowry', 'Oyster', 'Opal', 'MetroCard'],
            ['What does BRT stand for?', 'Bus Rapid Transit', 'Bus Road Ticket', 'Bridge Route Transport', 'Bus Rail Terminal'],
            ['What does LASTMA manage?', 'Road traffic', 'Water supply', 'Public schools', 'Waste collection'],
            ['Which agency oversees the Lagos rail lines?', 'LAMATA', 'LAWMA', 'LASAA', 'LASEPA'],
            ['After Marina, which Blue Line stop comes next toward Mile 2?', 'National Theatre', 'Alaba', 'Iganmu', 'Mile 2'],
            ['After Alaba, which Blue Line stop comes next toward Marina?', 'Iganmu', 'Mile 2', 'Oshodi', 'Marina'],
            ['Which rail line stops at National Theatre?', 'Blue Line', 'Red Line', 'Green Line', 'Purple Line'],
            ['Which rail line serves Mushin station?', 'Red Line', 'Blue Line', 'Green Line', 'Yellow Line'],
            ['After Yaba, which Red Line stop comes next toward Agbado?', 'Mushin', 'Agege', 'Oyingbo', 'Iju'],
            ['After Ikeja, which Red Line stop comes next toward Agbado?', 'Agege', 'Oshodi', 'Mushin', 'Yaba'],
            ['Which Red Line stop sits between Agege and Agbado?', 'Iju', 'Ikeja', 'Yaba', 'Oyingbo'],
            ['After Iganmu, which Blue Line stop comes next toward Mile 2?', 'Alaba', 'Marina', 'National Theatre', 'Oshodi'],
            ['Which rail line links Marina and Mile 2?', 'Blue Line', 'Red Line', 'Purple Line', 'Orange Line'],
            ['Which rail line links Oyingbo and Agbado?', 'Red Line', 'Blue Line', 'Green Line', 'Yellow Line'],
            ["Where do locals mean by 'Iyana Oworo'?", 'Oworonshoki', 'Ojota', 'Ogba', 'Ojo'],
            ['Which bridge links Lekki directly to Ikoyi?', 'Lekki–Ikoyi Link Bridge', 'Carter Bridge', 'Eko Bridge', 'Third Mainland Bridge'],
            ['Which bridge takes you from Idumota toward Iddo?', 'Carter Bridge', 'Eko Bridge', 'Lekki–Ikoyi Link Bridge', 'Falomo Bridge'],
            ['Apongbon is associated with which bridge?', 'Eko Bridge', 'Third Mainland Bridge', 'Lekki–Ikoyi Link Bridge', 'Falomo Bridge'],
            ["Which area is nicknamed 'AJ City'?", 'Ajegunle', 'Ajah', 'Ajao Estate', 'Agege'],
            ["Which city is nicknamed 'Lasgidi'?", 'Lagos', 'Ibadan', 'Abeokuta', 'Abuja'],
            ["What's the Yoruba name for Lagos?", 'Eko', 'Egba', 'Edo', 'Enugu'],
            ["Where's Iga Idunganran?", 'Isale Eko', 'Alausa', 'Festac', 'Magodo'],
            ['Who lives at Iga Idunganran?', 'The Oba of Lagos', 'The Akran of Badagry', 'The Ayangburen of Ikorodu', 'The Ooni of Ife'],
            ["What's Popo Aguda also called?", 'The Brazilian Quarter', 'The Railway Quarter', 'The French Quarter', 'The Government Quarter'],
            ['Which square is at the heart of Popo Aguda?', 'Campos Square', 'Tafawa Balewa Square', 'Tinubu Square', 'Glover Square'],
            ['Which returnee community is linked to Olowogbowo?', 'Saro', 'Aguda', 'Ijebu', 'Egba'],
            ['What was Freedom Park before it became a park?', 'Broad Street Prison', 'A railway station', 'A royal palace', 'A military airfield'],
            ["What is an Eyo masquerade's staff called?", 'Opambata', 'Agbada', 'Gele', 'Sekere'],
            ['What colour are traditional Eyo robes?', 'White', 'Red', 'Blue', 'Black'],
            ["What's another name for the Eyo festival?", 'Adamu Orisha', 'Osun Osogbo', 'Ojude Oba', 'Argungu'],
            ['What does the B in IBILE stand for?', 'Badagry', 'Bariga', 'Bamgbose', 'Berger'],
            ['What does the E in IBILE stand for?', 'Epe', 'Ebute Metta', 'Ejigbo', 'Ebutte Ero'],
            ['What does IBILE represent?', "Lagos's five administrative divisions", 'Five bus terminals', 'Five islands', 'Five railway stations'],
            ["Where's Jaekel House?", 'Ebute Metta Railway Compound', 'Marina', 'Alausa Secretariat', 'Lekki Phase 1'],
            ["What history does Jaekel House's museum focus on?", 'Railways', 'Aviation', 'Football', 'Oil exploration'],
            ['Which Ikeja street is home to Kalakuta Museum?', 'Gbemisola Street', 'Bourdillon Road', 'Broad Street', 'Awolowo Road, Ikoyi'],
            ['Whose former home became Kalakuta Museum?', 'Fela Kuti', 'King Sunny Ade', 'Ebenezer Obey', 'Bobby Benson'],
            ["Where's the New Afrika Shrine?", 'Agidingbi, Ikeja', 'Onikan', 'Badagry', 'Apapa'],
            ["Where's the National Theatre?", 'Iganmu', 'Ikoyi', 'Epe', 'Ogba'],
            ['Which Lagos nature spot has the canopy walkway?', 'Lekki Conservation Centre', 'Freedom Park', 'Tafawa Balewa Square', 'Ndubuisi Kanu Park'],
            ['Which town has the historic Point of No Return?', 'Badagry', 'Epe', 'Ikeja', 'Ikorodu'],
            ['Which area is home to the National Stadium?', 'Surulere', 'Ikoyi', 'Ogudu', 'Ojo'],
            ["Where's Lagos State's Secretariat?", 'Alausa', 'Ajegunle', 'Marina', 'Yaba'],
            ['Which event gave Festac Town its name?', 'FESTAC 77', 'Eyo festival', 'Lagos Carnival', 'The 1973 All-Africa Games'],
            ['Epe is especially associated with which trade?', 'Fishing', 'Car assembly', 'Diamond mining', 'Oil refining'],
            ['What does CMS stand for in old Lagos?', 'Church Missionary Society', 'Central Motor Station', 'City Market Square', 'Coastal Maritime Service'],
            ['Which Lagos Island waterfront is a banking district?', 'Marina', 'Makoko', 'Oko-Baba', 'Ajegunle'],
            ['Which community is known for homes on stilts?', 'Makoko', 'Magodo', 'Alausa', 'Ikeja GRA'],
            ['Which cathedral stands on the Marina?', 'Cathedral Church of Christ', 'Holy Cross Cathedral', 'St. Gregory', 'St. Leo'],
            ['Which beach area was reclaimed for Eko Atlantic?', 'Bar Beach', 'Elegushi Beach', 'Tarkwa Bay', 'Suntan Beach'],
            ["Where's the J. Randle Centre for Yoruba Culture?", 'Onikan', 'Ogba', 'Festac', 'Iyana Ipaja'],
            ["What's the traditional ruler of Ikorodu called?", 'Ayangburen', 'Akran', 'Oba of Lagos', 'Ooni'],
            ["What's the traditional ruler of Badagry called?", 'Akran', 'Ayangburen', 'Awujale', 'Alaafin'],
        ];

        return array_map(function (array $row, int $index) {
            [$prompt, $correct] = $row;
            $options = array_slice($row, 1);
            for ($shift = 0; $shift < $index % 4; $shift++) {
                $options[] = array_shift($options);
            }

            return [
                'question' => $prompt, 'option_a' => $options[0], 'option_b' => $options[1],
                'option_c' => $options[2], 'option_d' => $options[3],
                'answer' => 'option_' . ['a', 'b', 'c', 'd'][array_search($correct, $options, true)],
                'duration' => 60,
            ];
        }, $questions, array_keys($questions));
    }
}
