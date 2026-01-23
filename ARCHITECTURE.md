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

## 10. Dlaczego to jest dobre pod portfolio

* realny model biznesowy (multi-location)
* clean domain
* brak anty-patternów UX (relogin)
* pokazuje zrozumienie SaaS i multi-tenancy
* łatwe do opisania na rozmowie technicznej

---

**Status**: architektura gotowa do implementacji 🚀
