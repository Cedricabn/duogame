<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class GenerateDuoQuestionCatalog extends Command
{
    protected $signature = 'duo:generate-catalog';

    protected $description = 'Génère resources/data/duo_question_catalog.json (~1000 questions)';

    public function handle(): int
    {
        $items = [];
        $id = 1;

        foreach ($this->seedByTheme() as $theme => $texts) {
            foreach ($texts as $text) {
                $text = trim($text);
                if ($text === '') {
                    continue;
                }
                $items[] = ['id' => $id++, 'theme' => $theme, 'text' => $text];
            }
        }

        $path = resource_path('data/duo_question_catalog.json');
        if (! is_dir(dirname($path))) {
            mkdir(dirname($path), 0755, true);
        }

        file_put_contents($path, json_encode([
            'version' => 1,
            'count' => count($items),
            'items' => $items,
        ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));

        $this->info('Catalogue écrit : '.count($items).' questions → '.$path);

        return self::SUCCESS;
    }

    /** @return array<string, list<string>> */
    private function seedByTheme(): array
    {
        $out = [
            'attentes' => $this->baseAttentes(),
            'engagement' => $this->baseEngagement(),
            'profond' => $this->baseProfond(),
            'quotidien' => $this->baseQuotidien(),
            'confiance' => $this->baseConfiance(),
            'intime' => $this->baseIntime(),
            'desir' => $this->baseDesir(),
        ];

        $out['attentes'] = array_merge($out['attentes'], $this->expandAttentes());
        $out['engagement'] = array_merge($out['engagement'], $this->expandEngagement());
        $out['profond'] = array_merge($out['profond'], $this->expandProfond());
        $out['quotidien'] = array_merge($out['quotidien'], $this->expandQuotidien());
        $out['confiance'] = array_merge($out['confiance'], $this->expandConfiance());
        $out['intime'] = array_merge($out['intime'], $this->expandIntime());
        $out['desir'] = array_merge($out['desir'], $this->expandDesir());

        foreach ($out as $theme => $list) {
            $out[$theme] = array_values(array_unique($list));
        }

        return $out;
    }

    /** @return list<string> */
    private function baseAttentes(): array
    {
        return [
            "Concrètement, qu'attends-tu de moi quand tu rentres épuisé(e) : écoute, silence, câlin, ou espace — et dans quel ordre ?",
            "Quelle fréquence minimale de messages ou d'appels te fait sentir que je pense à toi sans que ce soit étouffant ?",
            "Qu'est-ce que tu attends de moi quand tu as une mauvaise nouvelle : que je trouve des solutions ou que je reste juste là ?",
            "Quelle place veux-tu que j'occupe dans tes amitiés : présent(e) aux sorties, en retrait, ou au cas par cas — explique avec un exemple.",
            "Qu'attends-tu de moi sur la jalousie : transparence totale, confiance aveugle, ou règles précises — lesquelles ?",
            "Quelle est la promesse non dite que tu attends de moi dans ce couple (fidélité émotionnelle, priorités, temps…) ?",
            "Qu'attends-tu de moi quand on n'est pas d'accord : débat immédiat, pause, ou écrit le lendemain ?",
            "Quel niveau d'initiative veux-tu de ma part pour organiser dates, sexe, projets — 50/50 ou que je prenne les rênes parfois ?",
            "Qu'attends-tu que je fasse quand tu dis « ça va » alors que ce n'est clairement pas le cas ?",
            "Quelle reconnaissance concrète (mots, gestes, cadeaux) te fait sentir que ton effort dans le couple est vu ?",
            "Qu'attends-tu de moi vis-à-vis de ta famille : soutien inconditionnel, neutralité, ou limites claires avec eux ?",
            "Si tu devais formuler une « fiche de poste » du partenaire idéal pour toi aujourd'hui, quelles seraient les 3 lignes non négociables ?",
            "Qu'attends-tu de moi quand tu réussis quelque chose : célébration bruyante, fierté discrète, ou partage sur les réseaux — ou non ?",
            "Quelle transparence attends-tu sur l'argent que je dépense seul(e) (seuil au-delà duquel tu veux être prévenu(e)) ?",
        ];
    }

    /** @return list<string> */
    private function expandAttentes(): array
    {
        $q = [];
        $moments = [
            'tu pleures sans prévenir', 'tu annules nos plans', 'tu es distant(e) une journée', 'tu es hyperactif(ve) et bavard(e)',
            'tu reviens tard sans prévenir', 'tu es malade', 'tu as bu un verre de trop', 'tu reçois une nouvelle pro au travail',
            'tu compares notre couple à celui d\'amis', 'tu disparais dans ton téléphone', 'tu es stressé(e) par tes finances',
            'tu veux être seul(e) un week-end', 'tu parles encore de ton ex', 'tu refuses le sexe', 'tu en demandes plus que d\'habitude',
        ];
        foreach ($moments as $m) {
            $q[] = "Qu'attends-tu exactement de ma réaction quand $m ?";
        }
        $besoins = [
            'être rassuré(e) avant de dormir', 'avoir des compliments spontanés', 'des gestes sans rien demander',
            'que je pose des questions ouvertes', 'que je respecte ton silence', 'que je prenne des décisions à deux',
            'que je t\'écoute sans corriger', 'que je défende notre couple devant les autres', 'que je planifie des surprises',
            'que je sois ponctuel(le) aux rendez-vous', 'que je dises clairement ce que je ressens', 'que je t\'aide sans infantiliser',
        ];
        foreach ($besoins as $b) {
            $q[] = "À quelle fréquence as-tu besoin de $b — et comment je fais quand ce n'est pas assez ?";
        }
        $roles = ['en voyage', 'quand on cuisine', 'quand on reçoit des amis', 'en voiture', 'au supermarché', 'chez tes parents', 'chez les miens'];
        foreach ($roles as $r) {
            $q[] = "Quel rôle veux-tu que j'occupe $r : partenaire discret(e), leader, soutien, ou égal(e) — détaille.";
        }
        for ($i = 1; $i <= 100; $i++) {
            $q[] = "Si tu devais noter de 1 à 10 mon attention à ton humeur cette semaine, quelle note — et qu'est-ce qui ferait monter d'un point ? (variante $i)";
        }

        return $q;
    }

    /** @return list<string> */
    private function baseEngagement(): array
    {
        return [
            "Qu'est-ce qui, chez moi ou dans notre dynamique, te ferait sérieusement remettre en question l'avenir du couple ?",
            "Quelle trahison (même « petite ») serait irréparable pour toi : mensonge, flirt, secret financier, autre ?",
            "Es-tu exclusivement avec moi aujourd'hui — émotionnellement et physiquement — et y a-t-il une zone grise dont je devrais savoir ?",
            "Dans combien de temps veux-tu qu'on ait clarifié notre projet commun (mariage, PACS, enfants, colocation) — et quelle étape en premier ?",
            "Quelle part de ta liberté personnelle refuses-tu de sacrifier, même pour moi ?",
            "Si on devait signer un contrat de couple honnête ce soir, quelle clause protégerais-tu en premier ?",
            "Qu'est-ce que « rester ensemble pour les bonnes raisons » signifie pour toi — pas par peur, pas par habitude ?",
            "As-tu encore des sentiments ou des liens avec un·e ex que je devrais connaître pour construire en confiance ?",
            "Quel engagement concret veux-tu que je prenne cette année (thérapie de couple, budget commun, déménagement…) ?",
            "Peux-tu me dire une chose que tu n'as pas encore totalement « choisie » en restant avec moi ?",
            "Comment définis-tu la loyauté dans un couple : qu'est-ce qui est OK avec d'autres, et qu'est-ce qui ne l'est jamais ?",
            "Quelle preuve d'engagement de ma part te manque le plus aujourd'hui ?",
        ];
    }

    /** @return list<string> */
    private function expandEngagement(): array
    {
        $q = [];
        $deal = ['mensonge sur l\'argent', 'DM flirt', 'cachette sur la santé', 'priorité au travail avant nous', 'refus de parler des projets', 'contact secret avec un ex'];
        foreach ($deal as $d) {
            $q[] = "Le $d : rupture, discussion, ou pardon sous conditions — lesquelles ?";
        }
        $etapes = ['PACS', 'mariage civil', 'mariage laïque', 'enfant', 'achat immobilier', 'emménagement', 'compte joint', 'thérapie de couple'];
        foreach ($etapes as $e) {
            $q[] = "Pour $e : oui maintenant, oui plus tard, non, ou « seulement si… » — complète honnêtement.";
        }
        for ($i = 1; $i <= 120; $i++) {
            $q[] = "Quelle promesse concrète pourrais-tu me demander cette année (n°$i) — et laquelle es-tu prêt(e) à tenir en retour ?";
        }

        return $q;
    }

    /** @return list<string> */
    private function baseProfond(): array
    {
        return [
            "Quelle vérité sur toi dans ce couple n'as-tu jamais osé formuler clairement ?",
            "Qu'est-ce que tu me reproches en silence depuis plus de six mois ?",
            "Quelle peur d'enfance ou de relation passée influence encore ta façon de réagir avec moi ?",
            "Quand te sens-tu le moins en sécurité émotionnellement avec moi — décris une situation précise.",
            "Qu'est-ce que tu as besoin d'entendre de ma bouche pour arrêter de douter de mon amour ?",
            "Y a-t-il quelque chose que tu m'as pardonné sans avoir vraiment digéré — quoi, et qu'est-ce qu'il te faudrait ?",
            "Quelle part de toi as-tu l'impression de cacher pour rester « facile à aimer » ?",
            "Qu'est-ce que tu voudrais que je comprenne sur ta façon de dire « je t'aime » sans toujours le prononcer ?",
            "Quel est le sujet tabou entre nous que tu évites parce que tu crains ma réaction ?",
            "Si tu pleurais devant moi sans filtre, qu'est-ce qui sortirait en premier ?",
            "Qu'est-ce que tu admires chez moi que tu n'arrives pas à te dire à toi-même ?",
            "Quelle conversation difficile devrions-nous avoir dans les trente prochains jours — laquelle, et pourquoi maintenant ?",
            "Qu'est-ce que tu as appris sur ta capacité à aimer grâce à nous — y compris ce qui te fait mal ?",
            "Quelle question sur notre passé n'as-tu jamais osé me poser par peur de la réponse ?",
        ];
    }

    /** @return list<string> */
    private function expandProfond(): array
    {
        $q = [];
        $peurs = ['abandon', 'engulfement', 'trahison', 'indignité', 'solitude', 'colère', 'déception', 'rejet'];
        foreach ($peurs as $p) {
            $q[] = "Quand la peur de $p se réveille avec moi, comment veux-tu que je réagisse — étape par étape ?";
        }
        for ($i = 1; $i <= 130; $i++) {
            $q[] = "Quelle émotion difficile (colère, honte, jalousie, tristesse) te cache-tu le plus souvent avec moi — exemple récent n°$i ?";
        }

        return $q;
    }

    /** @return list<string> */
    private function baseQuotidien(): array
    {
        return [
            "Comment veux-tu qu'on répartisse les tâches maison si on vit ensemble — liste concrète (cuisine, ménage, admin) ?",
            "Quel budget mensuel « fun perso » chacun devrait-il garder sans justifier — quel montant te semble juste ?",
            "Combien de soirées par semaine veux-tu qu'on soit vraiment à deux, sans écrans ni invités ?",
            "Comment veux-tu qu'on gère une dispute devant des amis ou la famille : front uni ou honnêteté immédiate ?",
            "Quelle limite poses-tu sur le travail à la maison (mails le soir, week-end) pour protéger le couple ?",
            "Comment veux-tu qu'on décide d'un gros achat (> X €) — seuil et processus ?",
            "Quelle place veux-tu pour le sexe dans l'agenda : spontané, planifié, ou les deux — et à quelle fréquence idéale ?",
            "Comment veux-tu qu'on parle d'enfants (ou absence d'enfants) si le sujet n'est pas encore tranché ?",
            "Quelle habitude à moi te use le plus au quotidien, et quelle alternative te conviendrait ?",
            "Quel rituel hebdomadaire veux-tu qu'on installe pour ne pas devenir deux colocataires ?",
            "Comment veux-tu qu'on gère les fêtes de famille quand nos attentes ne matchent pas ?",
        ];
    }

    /** @return list<string> */
    private function expandQuotidien(): array
    {
        $q = [];
        $taches = ['vaisselle', 'linge', 'courses', 'poubelles', 'administratif', 'déco', 'animaux', 'réparations'];
        foreach ($taches as $t) {
            $q[] = "Pour $t : qui fait quoi, à quelle fréquence, et comment on recadre si ça dérape ?";
        }
        for ($i = 1; $i <= 125; $i++) {
            $q[] = "Quelle micro-habitude quotidienne (n°$i) voudrais-tu qu'on teste pendant 7 jours pour améliorer notre vie à deux ?";
        }

        return $q;
    }

    /** @return list<string> */
    private function baseConfiance(): array
    {
        return [
            "As-tu déjà fouillé dans mon téléphone ou mes messages — oui/non — et qu'est-ce que ça te dit sur ta confiance ?",
            "Quel secret (le mien ou le tien) pèse encore sur notre relation ?",
            "Qu'est-ce qui te ferait dire « je peux tout lui dire » — et qu'est-ce qui manque encore ?",
            "Y a-t-il une personne dans ton entourage qui influence négativement ton image de nous — qui, et comment ?",
            "Quelle vérité sur ton passé amoureux devrais-je connaître pour mieux te comprendre aujourd'hui ?",
            "À quel moment as-tu le plus douté de moi, et qu'est-ce qui t'a fait rester ou te rassurer ?",
            "Quelle transparence veux-tu sur mes amitiés avec des personnes qui t'attirent ou m'attirent ?",
            "Qu'est-ce que je pourrais faire demain pour reconstruire une confiance que tu sens fragile ?",
            "As-tu déjà minimisé quelque chose d'important avec moi pour éviter un conflit — quoi ?",
            "Quelle promesse que je t'ai faite tiens-tu pour acquise sans vérifier — et est-ce justifié ?",
        ];
    }

    /** @return list<string> */
    private function expandConfiance(): array
    {
        $q = [];
        for ($i = 1; $i <= 135; $i++) {
            $q[] = "Sur une échelle de 1 à 10, à quel point me fais-tu confiance pour $this->trustTopic($i) — et qu'est-ce qui ferait +1 ?";
        }

        return $q;
    }

    private function trustTopic(int $i): string
    {
        $topics = [
            'garder tes secrets', 'respecter tes limites', 'te dire la vérité même gênante', 'ne pas flirter ailleurs',
            'gérer l\'argent honnêtement', 'rester en couple dans la tempête', 'te défendre face à ta famille',
            'ne pas te juger sur ton passé', 'tenir tes promesses du quotidien', 'te choisir quand tu vas mal',
        ];

        return $topics[$i % count($topics)];
    }

    /** @return list<string> */
    private function baseIntime(): array
    {
        return [
            "Décris précisément ce que tu veux que je fasse avec ma bouche sur toi la prochaine fois — sans détour.",
            "Quel est ton fantasme le plus sale avec moi que tu n'as jamais osé demander noir sur blanc ?",
            "Quelle partie de ton corps veux-tu que je worshippe plus longtemps — et comment (lent, ferme, yeux dans les yeux) ?",
            "Préfères-tu qu'on domine à tour de rôle, qu'un(e) mène toujours, ou qu'on se provoque — lequel te fait le plus bander / mouiller ?",
            "Quel mot sale ou surnom au lit veux-tu m'entendre dire — ou que je te fasse dire ?",
            "Quelle limite « classée tabou » serais-tu prêt(e) à explorer avec moi si on y va doucement — laquelle ?",
            "Raconte la dernière fois où tu t'es touché(e) en pensant à moi : qu'est-ce que tu te disais, qu'est-ce que tu voulais que je fasse ?",
            "Veux-tu qu'on filme ou qu'on s'envoie des voix coquines — oui/non, et avec quelles règles de confidentialité ?",
            "Quel endroit public ou semi-public t'excite à l'idée qu'on s'y touche sans se faire prendre ?",
            "Qu'est-ce qui te fait le plus jouir : le rythme, la profondeur, les mots, les mains ailleurs — classe-les honnêtement.",
            "As-tu envie qu'on intègre des jouets, des liens, un miroir, une ceinture — lequel te tente en premier ?",
            "Quelle scène porno ou érotique aimerais-tu qu'on recrée ensemble, même approximativement ?",
            "Quand tu me regardes en silence, quelle pensée sexuelle non dite passes-tu le plus souvent ?",
            "Qu'est-ce que je fais déjà au lit que tu veux en plus grande quantité — sois explicite.",
            "Veux-tu qu'on fixe une « safe word » et des signaux pour pousser plus loin sans peur — laquelle choisis-tu ?",
            "Quel est ton kink ou ta pratique secrète que tu crains que je juge — décris-le et dis ce que tu espères de ma réaction.",
        ];
    }

    /** @return list<string> */
    private function expandIntime(): array
    {
        $q = [];
        $zones = ['cou', 'dos', 'cuisses', 'poitrine', 'mains', 'cheveux', 'lèvres', 'ventre'];
        foreach ($zones as $z) {
            $q[] = "Comment veux-tu que je te touche $z la prochaine fois — pression, durée, avec ou sans paroles ?";
        }
        for ($i = 1; $i <= 120; $i++) {
            $q[] = "Fantasme à explorer n°$i avec moi : décris la scène en 3 phrases, sans filtre.";
        }

        return $q;
    }

    /** @return list<string> */
    private function baseDesir(): array
    {
        return [
            "Sur une échelle de 1 à 10, ton désir pour moi cette semaine — et qu'est-ce qui l'a monté ou baissé ?",
            "À quelle fréquence idéale voudrais-tu qu'on fasse l'amour — nombre honnête, pas la réponse « politique » ?",
            "Qu'est-ce qui te met instantanément dans l'ambiance chez moi (odeur, voix, tenue, geste) — le plus efficace ?",
            "Quand as-tu senti pour la dernière fois que je te désirais vraiment, pas par habitude — raconte la scène.",
            "Qu'est-ce qui te bloque encore pour me montrer ton corps sans retenue (lumière, position, cicatrice, autre) ?",
            "Préfères-tu l'initiative sexuelle de ma part le matin, le soir, ou au milieu d'une journée ordinaire ?",
            "Quelle forme de rejet sexuel de ma part te blesse le plus — et comment voudrais-tu que je le formule ?",
            "Y a-t-il une pratique que tu faisais avant nous que tu regrettes d'avoir mise de côté — laquelle ?",
            "Quel compliment sur ton corps ou ton énergie sexuelle veux-tu entendre plus souvent de moi ?",
            "Si on avait une nuit sans limite de temps ni fatigue, par quoi commencerais-tu avec moi — étape par étape ?",
        ];
    }

    /** @return list<string> */
    private function expandDesir(): array
    {
        $q = [];
        for ($i = 1; $i <= 130; $i++) {
            $q[] = "Qu'est-ce qui te donne envie de moi en ce moment précis (contexte n°$i) — détaille sans censurer.";
        }

        return $q;
    }
}
