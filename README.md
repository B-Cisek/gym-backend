# Gym Management SaaS - MVP

## Opis projektu

Gym Management SaaS to multi-tenant system dla sieci siłowni i trenerów personalnych, pozwalający na zarządzanie klientami, karnetami, grafikiem zajęć oraz check-inami. Aplikacja posiada trzy główne frontendy:

- **Web Admin (Nuxt)**: dla właścicieli i pracowników siłowni, pełne zarządzanie i raporty.
- **Mobile App (React Native)**: dla klientów i trenerów personalnych, umożliwiająca check-in, podgląd karnetu, grafik zajęć i powiadomienia.
- **Simple Web User (Nuxt)**: dla klientów, którzy nie chcą korzystać z aplikacji mobilnej.

System jest projektowany w podejściu **API-first** i z czystą architekturą.

### Kluczowe założenia

1. **Multi-tenant:**
    - Jedna aplikacja obsługuje wiele siłowni (gyms).
    - Dane są izolowane po gym_id.

2. **Globalni użytkownicy:**
    - Użytkownik może należeć do wielu siłowni.
    - Role nadawane są per siłownia.

3. **Hierarchia organizacyjna:**
    - **Organization**: firma / właściciel, może posiadać wiele lokalizacji.
    - **Gym**: lokalizacja siłowni, tenant operacyjny.

4. **Role i uprawnienia:**
    - **Organizational roles:** ORG_OWNER, ORG_ADMIN (zarządzanie organizacją i subskrypcją).
    - **Gym roles:** OWNER, MANAGER, STAFF, TRAINER (operacje w konkretnej siłowni).
    - **Client role:** użytkownik końcowy.

5. **Architektura API:**
    - REST API, versioning `/api/v1/...`
    - JWT + refresh tokens, z kontekstem gym_id i rolami.

6. **MVP Scope:**
    - Organizacje, siłownie, role.
    - Klienci, karnety, check-in.
    - Dashboard ownera: dodawanie siłowni, zarządzanie organizacją.

### Stack Technologiczny

- **PHP 8.5**
- **Symfony 8.0**
- **PostgreSQL 18.1**
- **Nginx 1.28**
- **Redis 8.0**


