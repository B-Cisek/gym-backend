# MVP
# Gym SaaS – podsumowanie architektury 
## 1. Cel systemu

System SaaS do zarządzania siecią siłowni:

* właściciel (Owner) wykupuje abonament
* owner może mieć wiele siłowni (lokalizacji)
* klienci kupują karnet u ownera i mogą wejść do wszystkich jego siłowni
* jeden backend (Symfony API)
* wiele klientów: web + mobile

---

## 2. Główne założenia architektoniczne

* **Multi-tenant (single database)**
* Tenantem jest **Owner (firma)**
* Jedno konto użytkownika = jedno logowanie
* Różne role wynikają z relacji, nie z osobnych systemów auth
* Brak relogowania przy zmianie siłowni

---

## 3. Główne byty domenowe

### User

Wspólny byt dla wszystkich:

* owner
* recepcjonista / staff
* klient

```sql
users
- id
- email
- password
- is_active
```

---

### Owner (firma)

```sql
owners
- id
- user_id
- name
```

* Owner = tenant
* Owner wykupuje abonament SaaS
* Owner posiada wiele siłowni

---

### Gym (lokalizacja)

```sql
gyms
- id
- owner_id
- name
- address
```

* Gym zawsze należy do jednego ownera
* Gym to tylko lokalizacja, nie byt billingowy

---

## 4. Karnety (logika biznesowa)

### Pass (karnet)

```sql
passes
- id
- owner_id
- name
- valid_days
- entry_limit
```

* Karnet jest przypisany do **ownera**, nie do siłowni
* Karnet daje dostęp do wszystkich siłowni ownera

---

### UserPass (karnet użytkownika)

```sql
user_passes
- id
- user_id
- pass_id
- valid_until
- active
```

Logika wejścia:

* user chce wejść do gym
* gym należy do ownera
* user ma aktywny karnet ownera

---

## 5. Personel (recepcja / trenerzy)

```sql
staff_memberships
- id
- user_id
- gym_id
- role (RECEPTION, TRAINER)
```

* staff przypisany do konkretnej siłowni
* jeden user może pracować w wielu siłowniach

---

## 6. Role i autoryzacja

### Role systemowe

* ROLE_OWNER
* ROLE_STAFF
* ROLE_MEMBER

Jedno konto użytkownika może mieć wiele ról jednocześnie.

---

## 7. Logowanie i kontekst

### Logowanie

* email + password
* JWT (identity only)

JWT zawiera:

```json
{
  "sub": 123,
  "roles": ["ROLE_OWNER", "ROLE_STAFF"]
}
```

---

### Kontekst siłowni

* Aktywna siłownia przekazywana w headerze:

```
X-Gym-Id: <gym_id>
```

* Brak potrzeby relogowania
* Backend sprawdza:

  * czy user ma dostęp do tej siłowni
  * czy siłownia należy do odpowiedniego ownera

---

## 8. Plany SaaS (abonamenty ownera)

```sql
plans
- id
- code (basic, pro)
- max_gyms
- max_staff_per_gym
```

```sql
subscriptions
- id
- owner_id
- plan_id
- active
```

Przykład:

* basic: max 2 siłownie
* pro: max 10 siłowni

Limity sprawdzane w warstwie serwisów (domain logic).

---

## 9. Najważniejsze decyzje projektowe

* Jeden backend dla web + mobile
* Jeden system logowania
* Karnet przypisany do ownera
* Gym jako kontekst, nie tenant
* Authorization oparta o relacje (Voters / Policies)

---

## 10. Struktura katalogów (Layered Architecture)

System oparty o architekturę warstwową z podziałem na cztery główne warstwy:

```
src/
├── Domain/          # Warstwa domenowa (encje, interfejsy, logika biznesowa)
├── Application/     # Warstwa aplikacyjna (use cases, komendy, query)
├── Infrastructure/  # Warstwa infrastruktury (implementacje, persistence)
└── Presentation/    # Warstwa prezentacji (API controllers, requests)
```

### Domain (`src/Domain/`)

Rdzeń biznesowy aplikacji – encje i logika domenowa:

* **Entity/** – encje domenowe (User, Gym, Pass, Owner)
* **Repository/** – interfejsy repozytoriów (bez implementacji)
* **Service/** – serwisy domenowe (czysta logika biznesowa)

---

### Application (`src/Application/`)

Przypadki użycia (use cases):

* **Command/** – komendy i handlery (zmiany stanu)
* **Query/** – zapytania i handlery (odczyt danych)
* **DTO/** – Data Transfer Objects

---

### Infrastructure (`src/Infrastructure/`)

Implementacje techniczne:

* **Persistence/** – repozytoria (Doctrine), ORM mapping, migracje
* **Security/** – authenticatory, voters, providery
* **External/** – integracje zewnętrzne (płatności, email)

---

### Presentation (`src/Presentation/`)

Warstwa API:

* **Controller/** – kontrolery API (thin, delegują do Application)
* **Request/** – walidacja inputu
* **Response/** – serializacja outputu
* **EventListener/** – event listeners dla HTTP lifecycle

---

### Zasady separacji warstw

1. **Domain** nie zależy od niczego (pure PHP)
2. **Application** zależy tylko od Domain
3. **Infrastructure** implementuje interfejsy z Domain
4. **Presentation** używa Application i Infrastructure

Przepływ danych:

```
Request → Controller (Presentation)
        → Command/Query (Application)
        → Domain Service/Repository (Domain)
        → Repository Implementation (Infrastructure)
        → Database
```

---

**Status**: architektura gotowa do implementacji 🚀
