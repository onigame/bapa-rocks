# System Documentation

## Architecture Overview
The application is a **Yii 2** PHP web application following the MVC pattern.
- **Frontend/Public:** standard controllers (`SessionController`, `MatchController`, etc.)
- **Backend/Admin:** specialized controllers prefixed with `Admin` (`AdminSessionController`, etc.)
- **Database:** Relational (MySQL/MariaDB expected).
- **Authentication:** `dektrium/yii2-user`.
- **RBAC:** `dektrium/yii2-rbac`.

## Key Entities & Terminology
- **Season**: A collection of sessions (e.g., "Fall 2023").
- **Session**: A single event (e.g., "Week 1"). Types: `1`=Regular, `2`=Playoff.
- **Match**: A grouping of players (2-4) playing together.
- **Game**: A single play of a machine within a match.
- **Eliminationgraph**: Defines the bracket structure for playoffs.

- **Machine**: Physical machine. Can be "Available", "Broken", or "In Use".
- **MachinePool**: Tracks player machine picks (not fully detailed but exists).

## User Flows

### Season & Machine Management
*   **Season**: Top-level container. Created via `AdminSeasonController`.
*   **Machine Management**: Machines have states (Available, Broken, Missing).
    *   **Queue**: `Machine::maybeStartQueuedGame` automatically promotes waiting games when a machine becomes free.


### Session Life Cycle
1.  **Creation**: Admin creates a Session (`Type 1` or `Type 2`).
2.  **Join**: Players join via `actionJoin` (self) or Admin adds them.
3.  **Start**: `SessionController::actionStart` locks the session.
    *   **Regular**: Groups players into matches of 3 or 4 based on attendance.
    *   **Playoff**: Seeds players based on Season stats and creates bracket matches using `Eliminationgraph`.
4.  **Play**: Matches are played (see Match Logic).
5.  **Finish**: `SessionController::actionFinish` calculates final points/stats and closes the session.

### Match Logic
*   **Formats**: Supports various formats (e.g., 4-player 4-game, Best of 3, etc.).
*   **Progression**: `Match::maybeStartGame` checks if a new game is needed.
*   **Completion**: `Match::completeMatch` calculates ranks/points.
    *   **Playoff Advance**: Winners/Losers are automatically moved to the next match code defined in `Eliminationgraph`.

### Game & Scoring Logic
*   **State Machine**:
    *   `0`: Awaiting Master Selector (who picks machine/order).
    *   `1`: Awaiting Machine/Player Order selection.
    *   `2`: Awaiting Machine availability (Queued).
    *   `3`: In Progress (Machine assigned, scores being entered).
    *   `4`: Completed.
*   **Queue System**: If a machine is in use, `QueueGame` holds the spot. `startOrEnqueueGame` manages this.
*   **Scoring**:
    *   Players enter scores via `Score` model.
    *   **Verification**: Separate `recorder_id` and `verifier_id` to ensure integrity.
    *   **Finish**: `Game::finishGame` locks via Mutex, assigns matchpoints (e.g., 4-2-1-0), and updates stats.
