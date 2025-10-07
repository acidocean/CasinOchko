<?php

// Подключаем все необходимые классы
require_once 'Card.php';
require_once 'Deck.php';
require_once 'Player.php';
require_once 'Dealer.php';
require_once 'gameLogic.php';

/**
 * Класс playGame — основной контроллер игры Очко (Блэкджек)
 *
 * Реализует:
 * - Начало новой игры
 * - Раздачу карт игроку и дилеру
 * - Получение текущего состояния игры (JSON)
 */
class playGame {

    /**
     * Начало новой игры
     *
     * Тасует колоду и раздаёт карты:
     * - Игроку — 2 карты
     * - Дилеру — 1 видимая и 1 скрытая карта
     *
     * @param Player $player Игрок
     * @param Dealer $dealer Дилер
     * @param Deck $deck Колода карт
     * @return array Ассоциативный массив с текущим состоянием игры
     */
    public static function startNewGame(Player $player, Dealer $dealer, Deck $deck) {
        // Перемешиваем колоду
        $deck->shuffle();

        // Раздаём карты
        $player->addCard($deck->draw());         // первая карта игроку
        $dealer->addCard($deck->draw(), false);  // видимая карта дилеру
        $player->addCard($deck->draw());         // вторая карта игроку
        $dealer->addCard($deck->draw(), true);   // скрытая карта дилеру

        // Возвращаем состояние игры
        return self::getGameState($player, $dealer, $deck);
    }

    /**
     * Получение текущего состояния игры
     *
     * Создаёт ассоциативный массив с информацией:
     * - Карты игрока
     * - Очки игрока
     * - Видимые карты дилера
     * - Количество скрытых карт дилера
     * - Очки дилера по видимым картам
     * - Количество оставшихся карт в колоде
     *
     * @param Player $player Игрок
     * @param Dealer $dealer Дилер
     * @param Deck $deck Колода
     * @return array Ассоциативный массив состояния игры
     */
    public static function getGameState(Player $player, Dealer $dealer, Deck $deck) {
        return [
            // Карты игрока (свойства: масть, ранг, значение)
            'playerCards' => array_map(function($c){
                return ['suit'=>$c->suit,'rank'=>$c->rank,'value'=>$c->value];
            }, $player->cards),

            // Текущий счет игрока
            'playerScore' => $player->getScore(),

            // Видимые карты дилера
            'dealerVisible' => array_map(function($c){
                return ['suit'=>$c->suit,'rank'=>$c->rank,'value'=>$c->value];
            }, $dealer->getVisibleCards()),

            // Количество скрытых карт дилера (обычно 1)
            'dealerHiddenCount' => count($dealer->getHiddenCards()),

            // Счет дилера по видимым картам
            'dealerScore' => $dealer->getScore(false),

            // Сколько карт осталось в колоде
            'deckCount' => $deck->getCount(),
        ];
    }

}
