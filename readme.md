# Conference Room Reservation System

## Krótki opis

Aplikacja do zarządzania rezerwacjami sal konferencyjnych. Umożliwia tworzenie rezerwacji, sprawdzanie dostępności sal w określonym czasie oraz walidację danych wejściowych. Aplikacja korzysta z bazy danych, obsługującą operacje za pomocą Doctrine ORM.

System wspiera również powiadomienia o nowych rezerwacjach, które są przesyłane do kolejki RabbitMQ w celu dalszego przetwarzania.

## Funkcjonalności:
- Rezerwacja sali konferencyjnej.
- Walidacja danych wejściowych (sprawdzanie poprawności formatu daty, dostępności sali).
- Możliwość sprawdzenia kolidujących rezerwacji.
- Obsługa błędów i odpowiedzi w formacie JSON.

## Wymagania:

- PHP 8.2+
- Composer
- Symfony 7.2+
- PostgreSQL

## Instalacja

### Krok 1: Klonowanie repozytorium

1. Sklonuj repozytorium:

```bash
git clone https://github.com/maciekryb/conference-room-reservation.git
```

2. Przejdź do katalogu projektu:

```bash
cd conference-room-reservation
```

### Krok 2: Instalacja zależności
1. Zainstaluj wszystkie zależności za pomocą Composer:

```bash
composer install
```

### Krok 3: Konfiguracja środowiska
1. Skopiuj plik `.env.example` do `.env`:

```bash
cp .env.example .env
```

2. Skonfiguruj parametry bazy danych PostgreSQL:

```env
DATABASE_URL="pgsql://db_user:db_password@127.0.0.1:5432/db_name"
```

### Krok 4: Tworzenie bazy danych
1. Stwórz bazę danych (jeśli jeszcze nie istnieje):

```bash
php bin/console doctrine:database:create
```

2. Wykonaj migracje:

```bash
php bin/console doctrine:migrations:migrate
```

### Krok 5: Uruchomienie serwera
1. Uruchom lokalny serwer Symfony:

```bash
php bin/console server:run
```

Aplikacja będzie dostępna pod adresem `http://127.0.0.1:8000`.

### Krok 6: Testowanie API
1. Możesz przetestować API używając Postmana lub CURL:
   Przykładowe żądanie POST do tworzenia rezerwacji:

```bash
curl -X POST http://127.0.0.1:8000/api/reservations \
-H "Content-Type: application/json" \
-d '{
  "conferenceRoomId": 1,
  "startTime": "2025-03-10T10:00:00",
  "endTime": "2025-03-10T12:00:00",
  "reservedBy": "Jan Kowalski"
}'
```

Przykładowa odpowiedź:

```json
{
  "message": "Reservation created successfully"
}
```
