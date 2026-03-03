# Gym SaaS - architektura systemu

## 1. Cel systemu

System SaaS do zarzadzania siecia silowni:

- owner (tenant) zarzadza swoim kontem i lokalizacjami (gym)
- wiele silowni w ramach jednego ownera
- jeden backend API dla web i mobile
- pojedyncze logowanie (JWT) dla uzytkownika

## 2. Multi-tenant

- Model: single database, tenantem jest `Owner`
- Izolacja danych: przez `owner_id`
- `Gym` jest kontekstem operacyjnym, nie tenantem

## 3. Aktualne moduly

Kod jest podzielony na moduly biznesowe:

- `Auth`
- `Owner`
- `Gym`
- `Subscription`
- `Shared` (cross-cutting)

Kazdy modul posiada warstwy:

- `Domain`
- `Application`
- `Infrastructure`
- `Presentation`

## 4. Glowne byty domenowe

Najwazniejsze byty obecne w kodzie:

- `Auth\Domain\User`
- `Owner\Domain\Owner`
- `Gym\Domain\Gym`
- `Subscription\Domain\Plan`
- `Subscription\Domain\PlanPrice`
- `Subscription\Domain\Subscription`

Dodatkowo w `Shared\Domain` znajduja sie wspolne value objecty i typy, np. `Id`, `Address`.

## 5. Logowanie i JWT

Autentykacja oparta o JWT (LexikJWTAuthenticationBundle).

Payload JWT zawiera dane tozsamosci i role, a dodatkowo:

- jezeli zalogowany user ma role owner, do payloadu dodawane jest `owner_id`
- `owner_id` jest ustawiane w listenerze `JWT_CREATED`
- `AuthContext` odczytuje `owner_id` z tokena i udostepnia go warstwom aplikacyjnym

Kontekst silowni przekazywany jest naglowkiem:

```http
X-Gym-Id: <gym_id>
```

## 6. Subscription i billing

Model subskrypcji ownera:

- `Plan` - poziom/tier oraz limity (np. gyms/staff)
- `PlanPrice` - warianty cenowe planu (Stripe price, interwal, kwota)
- `Subscription` - aktywna subskrypcja ownera

Integracja platnosci realizowana jest przez Stripe po stronie warstwy `Infrastructure`.

## 7. Struktura katalogow (Layered + modular)

```text
src/
├── Auth/
│   ├── Domain/
│   ├── Application/
│   ├── Infrastructure/
│   └── Presentation/
├── Owner/
│   ├── Domain/
│   ├── Application/
│   ├── Infrastructure/
│   └── Presentation/
├── Gym/
│   ├── Domain/
│   ├── Application/
│   ├── Infrastructure/
│   └── Presentation/
├── Subscription/
│   ├── Domain/
│   ├── Application/
│   ├── Infrastructure/
│   └── Presentation/
└── Shared/
    ├── Domain/
    ├── Application/
    ├── Infrastructure/
    └── Presentation/
```

## 8. Odpowiedzialnosci warstw

### Domain (`*/Domain/`)

- model domenowy i reguly biznesowe
- encje domenowe mapowane bezposrednio atrybutami Doctrine (`#[Entity]`, `#[Column]`, relacje)
- interfejsy repozytoriow
- value objecty i typy domenowe

### Application (`*/Application/`)

- przypadki uzycia (Command/Query)
- handlery komend i zapytan
- DTO/Result obiekty na granicy use case'ow

### Infrastructure (`*/Infrastructure/`)

- implementacje interfejsow z Domain (repozytoria Doctrine, query, adaptery)
- custom Doctrine types i integracje zewnetrzne (np. Stripe)
- elementy frameworkowe (security listeners, utilities)

### Presentation (`*/Presentation/`)

- kontrolery HTTP i komendy CLI
- mapowanie request/response
- delegowanie do Application

## 9. Zasady zaleznosci

1. `Application` zalezy od `Domain`
2. `Infrastructure` implementuje kontrakty z `Domain` i wspiera `Application`
3. `Presentation` korzysta z `Application` (+ pomocniczo z `Infrastructure`)
4. `Shared` zawiera elementy wspolne dla modulow

Przeplyw danych:

```text
Request -> Controller (Presentation)
       -> Command/Query (Application)
       -> Domain model + Repository interface (Domain)
       -> Repository/Adapter implementation (Infrastructure)
       -> Database / External service
```
